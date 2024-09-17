<?php

if ((string)Mage::getConfig()->getModuleConfig('Netzarbeiter_NicerImageNames')->active == 'true')
{
    class MageProfis_ImageQueue_Model_Catalog_Product_Image_Abstract
    extends Netzarbeiter_NicerImageNames_Model_Image
    { }
} elseif ((string)Mage::getConfig()->getModuleConfig('FireGento_PerfectWatermarks')->active == 'true')
{
    class MageProfis_ImageQueue_Model_Catalog_Product_Image_Abstract
    extends FireGento_PerfectWatermarks_Model_Product_Image
    { }
} else {
    class MageProfis_ImageQueue_Model_Catalog_Product_Image_Abstract
    extends Mage_Catalog_Model_Product_Image
    { }
}