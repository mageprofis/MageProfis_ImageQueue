<?php

class MageProfis_ImageQueue_Helper_Timage_Data
extends Technooze_Timage_Helper_Data
{
    public function resizer()
    {
        parent::resizer();
        Mage::helper('imagequeue')->addImageToCompress($this->cachedImage);
    }
}