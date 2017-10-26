<?php

if ((string)Mage::getConfig()->getModuleConfig('Netzarbeiter_NicerImageNames')->active == 'true')
{
    class Mage_Catalog_Model_Product_Image_Abstract
    extends Netzarbeiter_NicerImageNames_Model_Image
    { }
} else {
    class Mage_Catalog_Model_Product_Image_Abstract
    extends Mage_Catalog_Model_Product_Image
    { }
}