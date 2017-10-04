<?php

class MageProfis_ImageQueue_Model_Cron
extends Mage_Core_Model_Abstract
{
    protected $_limit = 100;

    protected $_command_exists = array();

    public function run()
    {
        if (!Mage::getStoreConfigFlag('imagequeue/general/active', 0))
        {
            return;
        }
        if (!Mage::getStoreConfigFlag('imagequeue/general/cron', 0))
        {
            return;
        }
        $collection = Mage::getModel('cron/schedule')->getCollection()
                        ->addFieldToFilter('status', Mage_Cron_Model_Schedule::STATUS_RUNNING)
                        ->addFieldToFilter('job_code', 'imagequeue_images');
        if($collection->getSize() > 1)
        {
            return 'Process already running';
        }
        $i = 0;
        while(true)
        {
            $item = Mage::getModel('imagequeue/compress')->getFirstItem();
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

            $extension = mb_strtolower(pathinfo($item->getFilename(), PATHINFO_EXTENSION), 'UTF-8');
            switch($extension)
            {
                case 'jpg':
                case 'jpeg':
                    $extension = 'jpeg';
                    break;
                case 'png':
                    $extension = 'png';
                    break;
            }
            
            if (!Mage::getStoreConfigFlag('imagequeue/general/debug', 0))
            {
                ob_start();
            }
            
            if ($extension == 'jpeg')
            {
                Mage::helper('imagequeue')->log('JPEG start compress: '.$item->getFilename());
                // jpegoptim
                $jpegoptimRunTwice = false;
                $jpegoptimExists = false;
                if (Mage::getStoreConfigFlag('imagequeue/programm/jpegoptim', 0) && $this->command_exist('jpegoptim'))
                {
                    $jpegoptimExists = true;
                    $this->shell_exec('jpegoptim -o --strip-all --max=90 --all-progressive "'.$item->getFilename().'" 2>&1');
                }

                // jpegtran, or mozjpeg
                if (Mage::getStoreConfigFlag('imagequeue/programm/jpegtran', 0) && $this->command_exist('jpegtran'))
                {
                    $jpegoptimRunTwice = true;
                    $this->shell_exec('jpegtran -copy none -optimize -progressive -outfile "'.$item->getFilename().'" "'.$item->getFilename().'" 2>&1');
                }

                // guetzli
                if (Mage::getStoreConfigFlag('imagequeue/programm/guetzli', 0) && $this->command_exist('guetzli'))
                {
                    $jpegoptimRunTwice = true;
                    $this->shell_exec('guetzli --quality 90 "'.$item->getFilename().'" "'.$item->getFilename().'" 2>&1');
                }

                // jpegoptim, twice, yep twice
                if ($jpegoptimRunTwice && $jpegoptimExists)
                {
                    $this->shell_exec('jpegoptim -o --strip-all --max=90 --all-progressive "'.$item->getFilename().'"');
                }
                Mage::helper('imagequeue')->log('JPEG end compress: '.$item->getFilename());
            } elseif ($extension == 'png') {
                Mage::helper('imagequeue')->log('PNG start compress: '.$item->getFilename());
                if (Mage::getStoreConfigFlag('imagequeue/programm/optipng', 0) && $this->command_exist('optipng'))
                {
                    $this->shell_exec('optipng -o9 -strip all "'.$item->getFilename().'" 2>&1');
                }

                if (Mage::getStoreConfigFlag('imagequeue/programm/pngquant', 0) && $this->command_exist('pngquant'))
                {
                    $this->shell_exec('pngquant --ext .png --force 256 "'.$item->getFilename().'" 2>&1');
                }
                Mage::helper('imagequeue')->log('PNG end compress: '.$item->getFilename());
            }

            if (!Mage::getStoreConfigFlag('imagequeue/general/debug', 0))
            {
                ob_end_clean();
            }
            $item->delete();
        }
    }

    /**
     * 
     * @param string $cmd
     * @return string
     */
    protected function shell_exec($cmd)
    {
        $result = shell_exec($cmd);
        if (Mage::getStoreConfigFlag('imagequeue/general/debug', 0))
        {
            echo date('r').' - '.trim($cmd)."\n";
            echo date('r').' - '.trim($result)."\n";
        }
        return $result;
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
}