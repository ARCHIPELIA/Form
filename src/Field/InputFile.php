<?php

namespace Atoll\Form\Field;

use Atoll\Form\AbstractFieldList;

class InputFile extends AbstractFieldList
{
  protected $size;
  protected $maxlength;
  protected $maxfilesize;

  protected $fileName = '';
  protected $fileExt = '';
  protected $uploaded = false;


  /** Constructeur **/
  public function __construct($id, $title, $accessKey, $size = 25, $maxlength = 255, $maxfilesize = 102400, $default = '')
  {
    parent::__construct($id, $title, $accessKey, $default);

    $this->size = $size;
    $this->maxlength = $maxlength;
    $this->maxfilesize = $maxfilesize;
  }

  /** setSize **/
  public function setSize($size)
  {
    $this->size = $size;
  }

  public function getMaxFileSize()
  {
    return $this->maxfilesize;
  }

  /** Affiche le champs de saisie texte **/
  public function showField($options = '')
  {
    if (!$this->disabled)
      $this->submited[] = $this->index;

    return '<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->maxfilesize . '"/>' .
    '<input type="file" class="input file' . ($this->isValid() ? '' : ' error') . '" id="' . $this->getId() . '" name="' . $this->getName() . '" size="' . $this->size . '" maxlength="' . $this->maxlength . '"' . ($this->highlight ? ' onblur="unhighlight(this);" onfocus="highlight(this);"' : '') .
    ($this->disabled ? ' disabled="true"' : '') . ($this->options != '' ? ' ' . $this->options : '') . ($options != '' ? ' ' . $options : '') . '/>';
  }

  /** isUploaded **/
  public function isUploaded()
  {
    return $this->uploaded;
  }

  /* getFileName */
  public function getFileName()
  {
    return $this->fileName;
  }

  /* getFileExt */
  public function getFileExt()
  {
    return $this->fileExt;
  }

  /* remove */
  public function remove()
  {
    if (is_file($this->fileName))
      return @unlink($this->fileName);

    return true;
  }

  /* rename */
  public function changeName($fileName, $force = false)
  {
    if ($force && is_file($fileName))
      @unlink($fileName);

    if (rename($this->fileName, $fileName)) {
      $this->fileName = $fileName;
      return true;
    }

    return false;
  }

  /* isSent */
  public function isSent()
  {
    if (!array_key_exists($fileId = $this->getId(), $_FILES))
      throw new \RuntimeException(__("Erreur de paramétrage du formulaire (enctype manquant) !"));

    return ($_FILES[$fileId]['error'] == UPLOAD_ERR_NO_FILE);
  }

  /* Récupération du fichier Uploadé : nécessite l'utilisation d'un champs formulaire InputFile */
  public function validate($path, $exts, &$error, $requis = true)
  {
    if (!array_key_exists($fileId = $this->getId(), $_FILES))
      throw new \RuntimeException(__("Erreur de paramétrage du formulaire (enctype manquant) !"));

    $fileNameTmp = $_FILES[$fileId]['tmp_name'];
    $fileUploadName = basename($_FILES[$fileId]['name']);
    $this->fileName = "$path/$fileUploadName";
    $this->fileExt = strtolower(substr($fileUploadName, strrpos($fileUploadName, '.') + 1));
    $this->uploaded = false;

    if (!is_uploaded_file($fileNameTmp)) {
      switch ($_FILES[$fileId]['error']) {
        case UPLOAD_ERR_INI_SIZE:    // Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.
        case UPLOAD_ERR_FORM_SIZE:   // Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
          $error = __("<b>%s</b> Aucun fichier uploadé ! Le fichier dépasse la taille maximale autorisée de %s Ko.", $this->getTitle(), number_format($this->getMaxFileSize() / 1024, 0, ',', ' '));
          return $this->setValid(false);
          break;

        case UPLOAD_ERR_NO_FILE:     // Value: 4; No file was uploaded.
          if ($requis) {
            $error = __("<b>%s</b> Aucun fichier uploadé ! Vérifiez que votre fichier ne dépasse pas %s Ko.", $this->getTitle(), number_format($this->getMaxFileSize() / 1024, 0, ',', ' '));

            return $this->setValid(false);
          } else {
            return $this->setValid(true);
          }
          break;

        case UPLOAD_ERR_NO_TMP_DIR:  // Value: 6; Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.
          $error = __("<b>%s</b> Aucun fichier uploadé ! Dossier temporaire inexistant.", $this->getTitle());
          return $this->setValid(false);
          break;

        case UPLOAD_ERR_CANT_WRITE:  // Value: 7; Failed to write file to disk. Introduced in PHP 5.1.0.
          $error = __("<b>%s</b> Aucun fichier uploadé ! Ecriture sur le disque impossible.", $this->getTitle());
          return $this->setValid(false);
          break;

        case UPLOAD_ERR_OK:          // Value: 0; There is no error, the file uploaded with success.
        case UPLOAD_ERR_PARTIAL:     // Value: 3; The uploaded file was only partially uploaded.
        default:
          $error = __("<b>%s</b> Aucun fichier uploadé ! Erreur indéterminée.", $this->getTitle());
          return $this->setValid(false);
          break;
      }
    }

    if (count($exts) > 0)
      if (!in_array($this->fileExt, $exts)) {
        // Extension non valide
        $error = __("<b>%s</b> Format non pris en charge (%s requis)", $this->getTitle(), implode(', ', $exts));
        return $this->setValid(false);
      }

    if (!file_exists($path))
      if (!mkdir($path, 0777, true)) {
        // Impossible créer le répertoire
        $error = __("<b>%s</b> Erreur d'écriture du fichier ! Veuillez recommencer l'importation.", $this->getTitle());
        return $this->setValid(false);
      }

    if (!move_uploaded_file($fileNameTmp, $this->fileName)) {
      // Impossible de déplacer le fichier
      $error = __("<b>%s</b> Erreur d'écriture du fichier ! Veuillez recommencer l'importation.", $this->getTitle());
      return $this->setValid(false);
    }

    $this->uploaded = true;

    return $this->setValid($this->uploaded);
  }
}
