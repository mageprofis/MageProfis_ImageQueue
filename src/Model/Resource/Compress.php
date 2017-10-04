<?php

class MageProfis_ImageQueue_Model_Resource_Compress
extends Mage_Core_Model_Resource_Db_Abstract
{
    
    protected function _construct()
    {
        $this->_init('imagequeue/compress', 'compress_id');
    }
}
