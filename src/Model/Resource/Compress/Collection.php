<?php

class MageProfis_ImageQueue_Model_Resource_Compress_Collection
extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('imagequeue/compress');
    }
}