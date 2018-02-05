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

    /**
     * @return string
     */
    public function getUrl()
    {
        if (Mage::helper('imagequeue')->canUseWebp())
        {
            $_newFile = $this->_newFile;
            $_newFileWebP = dirname($_newFile).DS.pathinfo($_newFile, PATHINFO_FILENAME).'.webp';
            if ($this->_fileExists($_newFileWebP))
            {
                $_newFile = $_newFileWebP;
            }
            $baseDir = Mage::getBaseDir('media');
            $path = str_replace($baseDir . DS, "", $_newFile);
            return Mage::getBaseUrl('media') . str_replace(DS, '/', $path);
        }
        return parent::getUrl();
    }
}
