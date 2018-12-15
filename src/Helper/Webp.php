<?php

class MageProfis_ImageQueue_Helper_Webp
extends Mage_Core_Helper_Abstract
{
    /**
     * 
     * @param string $source
     * @param string $destination
     * @return string
     */
    public function buildWebp($source, $destination)
    {
        var_dump($source, $destination);
        $quality = (int) Mage::getStoreConfig('imagequeue/webp/imagequality');
        if ($quality < 50)
        {
            $quality = 100;
        }
        $webpcli = 'cwebp';
        if (mime_content_type($source) == 'image/gif')
        {
            $webpcli = 'gif2webp';
        }
        return $this->shell_exec($webpcli.' -q '.$quality.' '.$this->escapeshellarg($source).' -o '.$this->escapeshellarg($destination).' 2>&1');
    }

    /**
     * 
     * @param string $cmd
     * @return string
     */
    public function escapeshellarg($cmd)
    {
        return escapeshellarg($cmd);
    }

    /**
     * 
     * @param string $cmd
     * @return string
     */
    protected function shell_exec($cmd)
    {
        return shell_exec($cmd);
    }
}