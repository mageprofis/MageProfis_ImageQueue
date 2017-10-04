<?php

class MageProfis_ImageQueue_Helper_Data
extends Mage_Core_Helper_Abstract
{
    /**
     * 
     * @param string $path
     * @return MageProfis_ImageQueue_Model_Compress
     */
    public function addImageToCompress($path, $prior = 0)
    {
        if (!Mage::getStoreConfigFlag('imagequeue/general/active', 0))
        {
            return;
        }
        $item = Mage::getModel('imagequeue/compress')->getCollection()
                ->addFieldToFilter('filename', $path)
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem();
        /* @var $item MageProfis_ImageQueue_Model_Resource_Compress_Collection */
        $compress = Mage::getModel('imagequeue/compress');
        /* @var $compress MageProfis_ImageQueue_Model_Compress */
        if ($item && $item->getId())
        {
            $compress->load($item->getId());
            if (intval($prior) > $compress->getPriority())
            {
                $prior = (int) $prior;
            } else {
                $prior = (int) $compress->getPriority();
            }
        }
        return $compress->setFilename($path)
                ->setPriority($prior)
                ->save();
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
}