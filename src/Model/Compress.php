<?php

/**
 * @method setFilename(string $name) set Filename
 * 
 * @method setPriority(string $prior) set Prior
 * @method getPriority() get Prior
 * 
 * @method MageProfis_ImageQueue_Model_Resource_Compress _getResource()
 * @method MageProfis_ImageQueue_Model_Resource_Compress getResource()
 * 
 * @method MageProfis_ImageQueue_Model_Resource_Compress_Collection getCollection()
 */
class MageProfis_ImageQueue_Model_Compress
extends Mage_Core_Model_Abstract
{
    
    protected function _construct()
    {
        $this->_init('imagequeue/compress');
    }

    public function getFilename()
    {
        return Mage::getBaseDir().DS.$this->getData('filename');
    }
    
    /**
     * Processing object before save data
     *
     * @return MageProfis_ImageQueue_Model_Compress
     */
    public function _beforeSave()
    {
        parent::_beforeSave();
        // prevent the fullpath in this list!
        $baseDir = Mage::getBaseDir();
        if (substr($this->getData('filename'), 0, strlen($baseDir)) == $baseDir)
        {
            $new = ltrim(ltrim(substr($this->getData('filename'), strlen($baseDir)), DS), '/');
            $this->setFilename($new);
        }
        return $this;
    }

    /**
     * @return MageProfis_ImageQueue_Model_Compress
     */
    public function getFirstItem($suffix = 'jpg')
    {
        return $this->getCollection()
                ->addOrder('priority', 'DESC')
                ->addOrder('compress_id', 'ASC')
                ->addFieldToFilter('suffix', $suffix)
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem();
    }
}
