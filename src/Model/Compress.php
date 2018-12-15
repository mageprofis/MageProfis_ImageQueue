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

    /**
     * 
     * @return string
     */
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
        if (!strlen($this->getData('filename_hash')) > 1 || !$this->hasData('filename_hash'))
        {
            $this->setData('filename_hash', hash('sha256', $this->getData('filename')));
        }
        $webbp = $this->getData('webbp');
        if (empty($webbp))
        {
            $this->setData('webbp', 0);
        }
        return $this;
    }

    /**
     * @return MageProfis_ImageQueue_Model_Compress
     */
    public function getFirstItem($suffix = 'jpg')
    {
        $collection = $this->getCollection();
        if ($suffix == 'webp')
        {
            $suffix = array(
                'in' => array('png', 'jpg')
            );
            $collection->addFieldToFilter('webp', 0);
        }
        return $collection
                ->addOrder('priority', 'DESC')
                ->addOrder('compress_id', 'ASC')
                ->addFieldToFilter('suffix', $suffix)
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem();
    }
}
