<?php

class MageProfis_ImageQueue_Helper_Timage_Data
extends Technooze_Timage_Helper_Data
{
    public function resizer()
    {
        $quality = (int) Mage::getStoreConfig('imagequeue/general/imagequality_gd', 0);
        if ($quality > 50)
        {
            $this->quality = $quality;
        }
        parent::resizer();
        Mage::helper('imagequeue')->addImageToCompress($this->cachedImage);
    }
    
    /**
     * @return string
     */
    public function cachedImageUrl()
    {
        if (Mage::helper('imagequeue')->canUseWebp())
        {
            $_cachedImage = $this->cachedImage;
            $_cachedImageWebP = dirname($_cachedImage).DS.pathinfo($_cachedImage, PATHINFO_FILENAME).'.webp';
            if (file_exists($_cachedImageWebP))
            {
                $_cachedImage = $_cachedImageWebP;
            }
            $img = str_replace(array(BP . DS . 'media', BP), '', $_cachedImage);
            $img = trim(str_replace('\\', '/', $img), '/');
            return $this->baseUrl . $img;
        }
        return parent::cachedImageUrl();
    }
}