<?php

abstract class MageProfis_ImageQueue_Model_Cms_Template_Filter_Abstract
extends Mage_Cms_Model_Template_Filter
{    
    protected $_generate_webp = false;
    
    public function __construct() {
        parent::__construct();
        if (Mage::getStoreConfigFlag('imagequeue/webp/generate_webp_image', 0))
        {
            $this->_generate_webp = true;
        }
    }

    public function mediaDirective($construction)
    {
        if (!Mage::helper('imagequeue')->canUseWebp())
        {
            return parent::mediaDirective($construction);
        }
        $params = $this->_getIncludeParameters($construction[2]);
        $path = $params['url'];
        $url = parent::mediaDirective($construction);

        if ($this->_generate_webp)
        {
            $path = rtrim(Mage::getBaseDir('media'), '/').'/'.$path;
            if (!$this->_isImage($path) || !file_exists($path))
            {
                return $url;
            }
            $pathWebp = dirname($path).DS. pathinfo($path, PATHINFO_FILENAME).'.webp';
            if(!file_exists($pathWebp))
            {
                Mage::helper('imagequeue/webp')->buildWebp($path, $pathWebp);
            }
        }
        return $this->getWebpImage($url);
    }
    
    public function getWebpImage($url)
    {
        if (Mage::helper('imagequeue')->canUseWebp())
        {
            $suffix = pathinfo($url, PATHINFO_EXTENSION);
            $suffixLength = abs(mb_strlen($suffix, 'UTF-8'));
            if ($suffixLength > 1)
            {
                $suffixLength = -1 * $suffixLength;
                $url = mb_substr($url, 0, $suffixLength, 'UTF-8').'webp';
            }
        }
        return $url;
    }

    /**
     * 
     * @param type $path
     * @return type
     */
    protected function _isImage($path)
    {
        $suffix = mb_strtolower(pathinfo($path, PATHINFO_EXTENSION), 'UTF-8');
        return in_array($suffix, array(
                'gif', 'png', 'jpeg', 'jpg'
        ));
    }
}