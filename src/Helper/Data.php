<?php

class MageProfis_ImageQueue_Helper_Data
extends Mage_Core_Helper_Abstract
{
    
    protected $_skipWebp = false;


    /**
     * 
     * @param string $path
     * @return MageProfis_ImageQueue_Model_Compress
     */
    public function addImageToCompress($path, $prior = 0)
    {
        if (!Mage::getStoreConfigFlag('imagequeue/general/active', 0))
        {
            return false;
        }
        if ($suffix = $this->allowedFile($path))
        {
            try {
                $compress = Mage::getModel('imagequeue/compress');
                /* @var $compress MageProfis_ImageQueue_Model_Compress */
                $compress->setFilename($path)
                        ->setPriority($prior)
                        ->setSuffix($suffix)
                        ->save();
            } catch(Exception $e) {
                Mage::logException($e);
            }
            return true;
        }
        return false;
    }

    /**
     * 
     * @param string $filename
     * @return string|null
     */
    public function allowedFile($filename)
    {
        $extension = mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION), 'UTF-8');
        $newextension = null;
        switch($extension)
        {
            case 'jpg':
            case 'jpeg':
                $newextension = 'jpg';
                break;
            case 'png':
                $newextension = 'png';
                break;
        }
        return $newextension;
    }

    /**
     * 
     * @param string $msg
     */
    public function log($msg)
    {
        if (Mage::getStoreConfigFlag('imagequeue/general/debug', 0))
        {
            echo date('r').' - '.$msg."\n";
        }
        Mage::log($msg, null, 'compress.log');
    }

    /**
     * 
     * @return boolean
     */
    public function canUseWebp()
    {
        if ($this->_skipWebp)
        {
            return false;
        }
        if (Mage::getStoreConfigFlag('imagequeue/programm/webp', 0))
        {
            if (isset($_SERVER['HTTP_ACCEPT']) && strstr($_SERVER['HTTP_ACCEPT'], 'image/webp'))
            {
                return true;
            }
            $ua = Mage::helper('core/http')->getHttpUserAgent();
            // okay pagespeed will be also webp able :)
            if (strstr($ua, 'Google Page Speed Insights'))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 
     * @param bool|int $bool
     * @return $this
     */
    public function setSkipcanUseWebp($bool)
    {
        $this->_skipWebp = (bool) intval($bool);
        return $this;
    }
}
