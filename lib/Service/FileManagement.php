<?php

namespace Service;

class FileManagement
{
    public const PATH = __DIR__.'/../../public/upload';
    public const REL_PATH = '/upload';
    protected const MAX_SIZE = 2000000;
    protected const ALL_EXT = [
        'png', 'jpeg', 'jpg', 'gif', 'pdf', 'doc', 'docx'
    ];
    protected const IMG_EXT = [
        'png', 'jpeg', 'jpg', 'gif'
    ];
    protected const TYPE = [
        'img', 'pdf', 'tiny', 'all'
    ];
    private $error;
    private $filename;
    private $size;

    public function deleteFile($filename, $type)
    {
        if (!$this->dirExist(self::PATH)) {
            return false;
        }
        if (!in_array($type, self::TYPE)) {
            $this->setError("Le type de fichier {".$type."} n'est pas pris en charge.");
            return false;
        }

        $path = self::PATH.'/'.$type.'/'.$filename;

        if (!file_exists($path)) {
            return true;
        }

        if (!unlink($path)) {
            $this->setError("Impossible de supprimer le fichier.");
            return false;
        }
        return true;
    }

    public function controlType($type)
    {
        if (!in_array($type, self::TYPE)) {
            $this->setError("Le type de fichier {".$type."} n'est pas pris en charge.");
            return false;
        }
        return true;
    }

    public function controlExtension($file, $type)
    {
        $ext = $this->getExtension($file['type']);
        switch ($type) {
            case 'img':
            case 'tiny':
                if (!$ext || !in_array($ext, self::IMG_EXT)) {
                    $this->setError("Extension de l'image non prise en charge {".$ext."}<br/>Extension autorisée : " . implode('<br />', self::IMG_EXT));
                 
                    return false;
                }
                break;
            case 'all':
                if (!$ext || !in_array($ext, self::ALL_EXT)) {
                    $this->setError("Extension du fichier non prise en charge {".$ext."}<br/>Extension autorisée : " . implode('<br />', self::ALL_EXT));
                
                    return false;
                }
                break;
            default:
                $this->setError('Le type de fichier n\'est pas défini.');
                
                return false;
        }

        return $ext;
    }

    public function controlSize($size)
    {
        if ($size > self::MAX_SIZE) {
            $this->setError('Le fichier est trop volumineux. Il doit être inférieur à 2Mo.');
            return false;
        }
        $this->size = $size;
        return true;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function uploadFile($file, $name, $type, $dir = '', $isReverse = false)
    {
        if (!$this->dirExist(self::PATH)) {
            return false;
        }
        
        if (!$extension = $this->controlExtension($file, $type)) {
            return false;
        }

        $matchExtension = false;
        
        foreach (self::IMG_EXT as $gExt) {
            if (preg_match('/'.$gExt.'/', $name)) {
                $matchExtension = true;
            }
        }

        if (!preg_match("/.".$extension."/", $name) && false === $matchExtension) {
            $this->setFilename($dir."/".$name.'.'.$extension);
        } else {
            $this->setFilename($dir."/".$name);
        }

        $oldDir = $dir;
        $dir = self::PATH."/".$type."/";
        
        if (!$this->dirExist($dir.$oldDir.'/')) {
            $this->setError('Le dossier n\'a pas été trouvé.');
            return false;
        }
        
        if (!move_uploaded_file($file['tmp_name'], $dir.$this->getFilename())) {
            $this->setError('Impossible de télécharger le fichier');
            return false;
        }

        if ($isReverse) {
            $this->saveReverseImage($name, $extension, $dir);
        }

        return true;
    }

    public function saveReverseImage($filename, $extension, $dir)
    {
        $matchExtension = false;
        
        foreach (FileManagement::IMG_EXT as $gExt) {
            if (preg_match('/'.$gExt.'/', $filename)) {
                $matchExtension = true;
                $cExt = $gExt;
            }
        }

        if (!preg_match("/.".$extension."/", $filename) && false === $matchExtension) {
            $filename = $filename.$extension;
        }

        $reverseFilename = $filename;

        switch ($cExt) {
            case 'png':
                $im = imagecreatefrompng($dir.$filename);
                // $im = imagecreatefrompng($dir.$this->getFilename());
                imageflip($im, IMG_FLIP_HORIZONTAL);
                imagepng($im, $dir.'r-product/'.$reverseFilename);
                imagedestroy($im);
                break;
            case 'jpg':
            case 'jpeg':
                $im = imagecreatefromjpeg($dir.$filename);
                // $im = imagecreatefromjpeg($dir.$this->getFilename());
                imageflip($im, IMG_FLIP_HORIZONTAL);
                imagejpeg($im, $dir.'r-product/'.$reverseFilename);
                imagedestroy($im);
                break;
        }
    }

    public function findError($error)
    {
        switch ($error) {
            case 4:
                $this->setError('Aucun fichier');
                return true;
            default:
                return false;
        }
    }

    /**
     * return extension of type
     *
     * @param string $type
     * @return bool|string
     */
    public function getExtension(string $type)
    {
        if (!preg_match('#/#', $type)) {
            $this->setError('Impossible de récupérer l\'extension.');
            
            return false;
        }
        
        $temp = explode('/', $type);

        if (empty($temp[1]) || !isset($temp[1])) {
            $this->setError('Format d\'extension non reconnu.');
            
            return false;
        }

        return $temp[1];
    }

    public function dirExist(string $dir)
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                $this->setError("Impossible de créé le dossier cible {".$dir."}");
            }

            return false;
        }

        return true;
    }

    /**
     * Get the value of error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set the value of error
     *
     * @return  self
     */
    private function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get the value of filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the value of filename
     *
     * @return  self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }
}
