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

    /**
     * Retrieve media file URL directive
     *
     * @param array $construction
     * @return string
     */
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

    /**
     * Retrieve Skin URL directive
     *
     * @param array $construction
     * @return string
     */
    public function skinDirective($construction)
    {
        $url = parent::skinDirective($construction);
        if (!Mage::helper('imagequeue')->canUseWebp())
        {
            return $url;
        }
        
        $path = parse_url($url, PHP_URL_PATH);
        $path = rtrim(Mage::getBaseDir('base'), '/').'/'.$path;
        $pathWebp = dirname($path).DS. pathinfo($path, PATHINFO_FILENAME).'.webp';
        if (!$this->_isImage($path) || !file_exists($pathWebp))
        {
            return $url;
        }
        return $this->getWebpImage($url);
    }

    /**
     * 
     * @param string $url
     * @return string
     */
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
     * @param string $path
     * @return bool
     */
    protected function _isImage($path)
    {
        $suffix = mb_strtolower(pathinfo($path, PATHINFO_EXTENSION), 'UTF-8');
        return in_array($suffix, array(
                'gif', 'png', 'jpeg', 'jpg'
        ));
    }
}
