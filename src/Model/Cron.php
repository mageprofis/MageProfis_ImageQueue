<?php

class MageProfis_ImageQueue_Model_Cron
extends Mage_Core_Model_Abstract
{
    protected $_limit = 100;

    protected $_command_exists = array();

    public function runJpg()
    {
        if (!$this->getConfigFlag('imagequeue/general/active'))
        {
            return;
        }
        if (!$this->getConfigFlag('imagequeue/general/cron'))
        {
            return;
        }
        $collection = Mage::getModel('cron/schedule')->getCollection()
                        ->addFieldToFilter('status', Mage_Cron_Model_Schedule::STATUS_RUNNING)
                        ->addFieldToFilter('job_code', 'imagequeue_images_jpg');
        if($collection->getSize() > 1)
        {
            return 'Process already running';
        }
        $i = 0;
        while(true)
        {
            $item = Mage::getModel('imagequeue/compress')->getFirstItem('jpg');
            /* @var $item MageProfis_ImageQueue_Model_Compress */
            // skip at range or if there is no item in queue
            if ($i >= $this->_limit || (!$item || !$item->getId()))
            {
                break;
            }
            if (!file_exists($item->getFilename()))
            {
                $item->delete();
                continue;
            }
            $i++;
            
            if (!$this->getConfigFlag('imagequeue/general/debug'))
            {
                ob_start();
            }
            Mage::helper('imagequeue')->log('JPEG start compress: '.$item->getFilename());
            // jpegoptim
            $jpegoptimRunTwice = false;
            $jpegoptimExists = false;

            $this->_buildWebp($item);

            if ($this->getConfigFlag('imagequeue/programm/jpegoptim') && $this->command_exist('jpegoptim'))
            {
                $jpegoptimExists = true;
                $quality = (int) $this->getConfig('imagequeue/imagequality/jpeg');
                if ($quality < 50)
                {
                    $quality = 100;
                }
                $this->shell_exec('jpegoptim -o --strip-all --max='.$quality.' --all-progressive '.$this->escapeshellarg($item->getFilename()).' 2>&1');
            }

            // jpegtran, or mozjpeg
            if ($this->getConfigFlag('imagequeue/programm/jpegtran') && $this->command_exist('jpegtran'))
            {
                $jpegoptimRunTwice = true;
                $this->shell_exec('jpegtran -copy none -optimize -progressive -outfile '.$this->escapeshellarg($item->getFilename()).' '.$this->escapeshellarg($item->getFilename()).' 2>&1');
            }

            // guetzli
            if ($this->getConfigFlag('imagequeue/programm/guetzli') && $this->command_exist('guetzli'))
            {
                $jpegoptimRunTwice = true;
                $quality = (int) $this->getConfig('imagequeue/imagequality/jpeg');
                if ($quality < 50)
                {
                    $quality = 100;
                }
                $this->shell_exec('guetzli --quality '.$quality.' '.$this->escapeshellarg($item->getFilename()).' '.$this->escapeshellarg($item->getFilename()).' 2>&1');
            }

            // jpegoptim, twice, yep twice
            if ($jpegoptimRunTwice && $jpegoptimExists)
            {
                $this->shell_exec('jpegoptim -o --strip-all --all-progressive '.$this->escapeshellarg($item->getFilename()).'');
            }

            $this->_removeWebp($item);

            Mage::helper('imagequeue')->log('JPEG end compress: '.$item->getFilename());

            if (!$this->getConfigFlag('imagequeue/general/debug'))
            {
                ob_end_clean();
            }
            Mage::getModel('imagequeue/compress')->load($item->getId())->delete();
        }
    }

    /**
     * 
     * @return string
     */
    public function runPng()
    {
        if (!$this->getConfigFlag('imagequeue/general/active'))
        {
            return;
        }
        if (!$this->getConfigFlag('imagequeue/general/cron'))
        {
            return;
        }
        $collection = Mage::getModel('cron/schedule')->getCollection()
                        ->addFieldToFilter('status', Mage_Cron_Model_Schedule::STATUS_RUNNING)
                        ->addFieldToFilter('job_code', 'imagequeue_images_png');
        if($collection->getSize() > 1)
        {
            return 'Process already running';
        }
        $i = 0;
        while(true)
        {
            $item = Mage::getModel('imagequeue/compress')->getFirstItem('png');
            /* @var $item MageProfis_ImageQueue_Model_Compress */
            // skip at range or if there is no item in queue
            if ($i >= $this->_limit || (!$item || !$item->getId()))
            {
                break;
            }
            if (!file_exists($item->getFilename()))
            {
                $item->delete();
                continue;
            }
            $i++;
            
            if (!$this->getConfigFlag('imagequeue/general/debug'))
            {
                ob_start();
            }

            $this->_buildWebp($item);

            Mage::helper('imagequeue')->log('PNG start compress: '.$item->getFilename());
            if ($this->getConfigFlag('imagequeue/programm/optipng') && $this->command_exist('optipng'))
            {
                $this->shell_exec('optipng -o9 -strip all '.$this->escapeshellarg($item->getFilename()).' 2>&1');
            }

            if ($this->getConfigFlag('imagequeue/programm/pngquant') && $this->command_exist('pngquant'))
            {
                $this->shell_exec('pngquant --skip-if-larger --ext .png --force 256 '.$this->escapeshellarg($item->getFilename()).' 2>&1');
            }
            Mage::helper('imagequeue')->log('PNG end compress: '.$item->getFilename());

            $this->_removeWebp($item);

            if (!$this->getConfigFlag('imagequeue/general/debug'))
            {
                ob_end_clean();
            }
            Mage::getModel('imagequeue/compress')->load($item->getId())->delete();
        }
    }

    /**
     * 
     * @return string
     */
    public function runWebp()
    {
        if (!$this->getConfigFlag('imagequeue/general/active'))
        {
            return;
        }
        if (!$this->getConfigFlag('imagequeue/general/cron'))
        {
            return;
        }
        $collection = Mage::getModel('cron/schedule')->getCollection()
                        ->addFieldToFilter('status', Mage_Cron_Model_Schedule::STATUS_RUNNING)
                        ->addFieldToFilter('job_code', 'imagequeue_images_webp');
        if($collection->getSize() > 1)
        {
            return 'Process already running';
        }
        $i = 0;
        while(true)
        {
            $item = Mage::getModel('imagequeue/compress')->getFirstItem('webp');
            /* @var $item MageProfis_ImageQueue_Model_Compress */
            // skip at range or if there is no item in queue
            if ($i >= $this->_limit || (!$item || !$item->getId()))
            {
                break;
            }
            if (!file_exists($item->getFilename()))
            {
                $item->delete();
                continue;
            }
            $i++;
            
            if (!$this->getConfigFlag('imagequeue/general/debug'))
            {
                ob_start();
            }

            Mage::helper('imagequeue')->log('WEBP start compress: '.$item->getFilename());

            $this->_buildWebp($item);

            Mage::helper('imagequeue')->log('PNG end compress: '.$item->getFilename());

            $this->_removeWebp($item);

            if (!$this->getConfigFlag('imagequeue/general/debug'))
            {
                ob_end_clean();
            }
            $item = Mage::getModel('imagequeue/compress')->load($item->getId());
            if ($item && $item->getId())
            {
                $item->setData('webp', 1)
                        ->save();
            }
        }
    }

    /**
     * 
     * @param string $cmd
     * @return string
     */
    protected function shell_exec($cmd)
    {
        $this->disconnectDatabases();
        $result = shell_exec($cmd);
        if ($this->getConfigFlag('imagequeue/general/debug'))
        {
            echo date('r').' - '.trim($cmd)."\n";
            echo date('r').' - '.trim($result)."\n";
        }
        $this->disconnectDatabases();
        return $result;
    }

    /**
     * 
     * @param string $cmd
     * @return string
     */
    public function escapeshellarg($cmd)
    {
        return escapeshellarg($cmd);
    }
    
    /**
     * 
     * @param string $cmd
     * @return bool
     */
    protected function command_exist($cmd)
    {
        if (!isset($this->_command_exists[$cmd]))
        {
            $return = shell_exec(sprintf("which %s", escapeshellarg($cmd)));
            $result = !empty($return);
            $this->_command_exists[$cmd] = $result;
        }
        return $this->_command_exists[$cmd];
    }

    /**
     * to avoid errors with missing
     * 
     * @return void
     */
    public function disconnectDatabases()
    {
        $resource = Mage::getSingleton('core/resource');
        /* @var $resource Mage_Core_Model_Resource */

        // write connection
        $conn = $resource->getConnection('core_write');
        /* @var $conn Magento_Db_Adapter_Pdo_Mysql */
        try {
            $conn->fetchOne('SELECT 42 as answer');
        } catch (Exception $ex) {
            if (stristr($ex->getMessage(), 'MySQL server has gone away'))
            {
                $conn->closeConnection();
            }
        }

        // read connection
        $conn = $resource->getConnection('core_read');
        /* @var $conn Magento_Db_Adapter_Pdo_Mysql */
        try {
            $conn->fetchOne('SELECT 42 as answer');
        } catch (Exception $ex) {
            if (stristr($ex->getMessage(), 'MySQL server has gone away'))
            {
                $conn->closeConnection();
            }
        }
    }

    /**
     * 
     * @param MageProfis_ImageQueue_Model_Compress $item
     * @return type
     */
    protected function _getWebpFilename(MageProfis_ImageQueue_Model_Compress $item)
    {
        return dirname($item->getFilename()).DS. pathinfo($item->getFilename(), PATHINFO_FILENAME).'.webp';
    }

    /**
     * 
     * @param MageProfis_ImageQueue_Model_Compress $item
     * @return boolean
     */
    protected function _buildWebp(MageProfis_ImageQueue_Model_Compress $item)
    {
        if ($this->getConfigFlag('imagequeue/programm/webp') && $this->command_exist('cwebp'))
        {
            $webpFilename = $this->_getWebpFilename($item);
            Mage::helper('imagequeue/webp')->buildWebp($item->getFilename(), $webpFilename);
        }
        return true;
    }

    /**
     * 
     * @param MageProfis_ImageQueue_Model_Compress $item
     */
    protected function _removeWebp(MageProfis_ImageQueue_Model_Compress $item)
    {
        if ($this->getConfigFlag('imagequeue/programm/webp') && $this->command_exist('cwebp')
                && $this->getConfigFlag('imagequeue/webp/remove_files_on_size'))
        {
            $webpFilename = $this->_getWebpFilename($item);
            $webpFilenameSize = filesize($webpFilename);
            $filenameSize = filesize($item->getFilename());
            if ($webpFilenameSize >= $filenameSize)
            {
                @unlink($webpFilename);
            }
        }
    }

    /**
     * 
     * @param string $path
     * @return string
     */
    protected function getConfig($path)
    {
        return Mage::getStoreConfig($path, 0);
    }

    /**
     * 
     * @param string $path
     * @return bool
     */
    protected function getConfigFlag($path)
    {
        return Mage::getStoreConfigFlag($path, 0);
    }
}
