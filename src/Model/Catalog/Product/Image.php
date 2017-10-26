<?php

class MageProfis_ImageQueue_Model_Catalog_Product_Image
extends MageProfis_ImageQueue_Model_Catalog_Product_Image_Abstract
{
    /**
     * @return MageProfis_ImageQueue_Model_Catalog_Product_Image
     */
    public function saveFile()
    {
        parent::saveFile();
        Mage::helper('imagequeue')->addImageToCompress($this->getNewFile());
        return $this;
    }
}
