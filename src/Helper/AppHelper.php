<?php

/** TODO : Classes a importer
 * Page
 * JSON
 * EMailAdress
 * EMail
 * SqlBdD
 */
if(!function_exists('define_prm')) {
  function define_prm($constant, $val)
  {
    if (!defined($constant))
      define($constant, $val);
  }
}
if (!defined(PATH_ROOT_WEB)) define_prm('PATH_ROOT_WEB', '');

if (!defined(PATH_INC_WEB)) define_prm('PATH_INC_WEB', PATH_ROOT_WEB . '/sysgestion/include_web');

if(!function_exists('showLink')) {
  function showLink($href, $text = '', $option = '', $accessKey = '', $img_url = '', $img_title = '', $img_padding = 3, $img_option = "")
  {
    if ($text != '' && $accessKey !== null) {
      $accessKey = ($accessKey != '' && strpos($text, $accessKey) !== false) ? $accessKey : substr(trim($text), 0, 1);
      $text = preg_replace("/([^$accessKey]*)($accessKey)(.*)/i", "\\1<u>\\2</u>\\3", $text);
    }

    $txt_img = ($img_url != '') ? showIco($img_url, $text, $img_title, $img_padding, $img_option) : $text;

    return '<a href = "' . $href . '" ' . ($accessKey != '' ? ' accesskey="' . $accessKey . '"' : '') . ' ' . ($option != '' ? ' ' . $option : '') . ' class="showlink">' . $txt_img . '</a>';
  }
}

if(!function_exists('validate')) {
  function validate($champ, $type, $requis, $taille_max, &$msg)
  {
    if ($requis && (strlen($champ) == 0)) {
      $msg = __("Champ requis !");
      return false;
    }

    if ((strlen($champ) > $taille_max) and ($taille_max != 0)) {
      $msg = __("Champ trop long ( <= %s caractères )", $taille_max);
      return false;
    }

    if (strlen($champ) != 0) {
      if ($type != '') {
        switch ($type) {

          // Vérifie un email
          case "email":
            if (!email_OK($champ)) {
              $msg = __("Format ou nom de domaine e-mail non valides !");
              return false;
            }
            break;

          // Vérifie des emails
          case "emails":
            if (!emails_OK($champ)) {
              $msg = __("Format ou nom de domaine e-mail non valides !");
              return false;
            }
            break;

          // Vérifie un telephone
          case "tel":
            if (!tel_OK($champ)) {
              $msg = __("Numéro non valide !");
              return false;
            }
            break;

          // Vérifie un int
          case "int":
            if (!int_OK($champ)) {
              $msg = __("Nombre entier non valide !");
              return false;
            }
            break;

          // Vérifie un int non nul supérieur à 0
          case "int+":
            if (!int_OK($champ) || !($champ > 0)) {
              $msg = __("Nombre entier non valide !");
              return false;
            }
            break;

          // Vérifie une chaine de caractères
          case "caract":
            if (!caract_OK($champ)) {
              $msg = __("Chaîne de caractère non valide !");
              return false;
            }
            break;

          // Vérifie un int de $taille_max caractères
          case "int_full":
            if ((strlen($champ) != $taille_max) and ($taille_max != 0)) {
              $msg = __("Champ incomplet ( %s caractères )", $taille_max);
              return false;
            }
            if (!int_OK($champ)) {
              $msg = __("Nombre entier non valide !");
              return false;
            }
            break;

          // Vérifie un float
          case "float":
            if (!float_OK($champ)) {
              $msg = __("Nombre non valide !");
              return false;
            }
            break;

          // Vérifie un float positif ou négatif
          case "float-":
            if (!float_neg_OK($champ)) {
              $msg = __("Nombre non valide !");
              return false;
            }
            break;

          // Vérifie un float non null
          case "float+":
            if (!float_OK($champ) || !(phpFloat($champ) > 0)) {
              $msg = __("Nombre non valide !");
              return false;
            }
            break;

          // Vérification d'un nombre (10, 2)
          case "num_10_2":
            if (!cur_10_2_OK($champ)) {
              $msg = __("Nombre non valide !");
              return false;
            }
            break;

          // Vérification d'un tarif (10, 2)
          case "cur_10_2":
            if (!cur_10_2_OK($champ)) {
              $msg = __("Champ monétaire non valide !");
              return false;
            }
            break;

          // Vérification d'un nombre (11, 3)
          case "num_11_3":
            if (!cur_11_3_OK($champ)) {
              $msg = __("Nombre non valide !");
              return false;
            }
            break;

          // Vérification d'un nombre (11, 3)
          case "num_11_3+":
            if (!cur_11_3_OK($champ) || !(phpFloat($champ) > 0)) {
              $msg = __("Nombre non valide !");
              return false;
            }
            break;

          // Vérification d'un tarif (11, 3)
          case "cur_11_3":
            if (!cur_11_3_OK($champ)) {
              $msg = __("Champ monétaire non valide !");
              return false;
            }
            break;

          // Vérification d'un tarif (11, 3) strictement positif
          case "cur_11_3+":
            if (!cur_11_3_OK($champ)) {
              $msg = __("Champ monétaire non valide !");
              return false;
            } else if (!(phpFloat($champ) > 0)) {
              $msg = __("Le champ monétaire doit étre supérieur à 0 !");
              return false;
            }
            break;

          // Vérification d'un tarif (11, 3)
          case "cur_11_3-":
            if (!cur_11_3_neg_OK($champ)) {
              $msg = __("Champ monétaire non valide !");
              return false;
            }
            break;

          // Vérification d'un tarif (13, 3)
          case "cur_13_3":
            if (!cur_13_3_OK($champ)) {
              $msg = __("Champ monétaire non valide !");
              return false;
            }
            break;

          // Vérification d'un tarif (15, 3)
          case "cur_15_3":
            if (!cur_15_3_OK($champ)) {
              $msg = __("Champ monétaire non valide !");
              return false;
            }
            break;

          // Vérification d'un tarif (13, 5) : stock & inventaire
          case "cur_13_5":
            if (!cur_13_5_OK($champ)) {
              $msg = __("Champ monétaire non valide !");
              return false;
            }
            break;

          // Vérification d'un poids
          case "poids":
            if (!cur_11_3_OK($champ)) {
              $msg = __("Poids non valide !");
              return false;
            }
            break;

          // Vérification d'un pourcentage <= 100%
          case "%<100":
            if (!float_OK($champ)) {
              $msg = __("Pourcentage non valide !");
              return false;
            }
            if (phpFloat($champ) > 100) {
              $msg = __("Le pourcentage ne peut être supérieur à 100% !");
              return false;
            }
            break;

          // Vérification d'un pourcentage <= 100%
          case "%<100-":
            if (!float_neg_OK($champ)) {
              $msg = __("Pourcentage non valide !");
              return false;
            }
            if (phpFloat(abs($champ)) > 100) {
              $msg = __("Le pourcentage ne peut être supérieur à 100% !");
              return false;
            }
            break;

          // Vérification d'une date (j)j/(m)m/aaaa
          case "date_normal":
            if (!date_normal_OK($champ)) {
              $msg = __("Date non valide !");
              return false;
            }
            break;

          // Vérification d'une heure hh:mi
          case "heure_normal":
            if (!heure_normal_OK($champ)) {
              $msg = __("Heure non valide !");
              return false;
            }
            break;

          // Vérification d'une heure hh:mi:ss
          case "heure_minute":
            if (!heure_minute_OK($champ)) {
              $msg = __("Heure non valide !");
              return false;
            }
            break;

          // Vérification d'une période (m)m/aaaa
          case "periode":
            if (!date_periode_OK($champ)) {
              $msg = __("Période non valide !");
              return false;
            }
            break;

          // Vérification d'une période (s)s/aaaa
          case "week_year":
            if (!date_week_year_OK($champ)) {
              $msg = __("Période non valide !");
              return false;
            }
            break;

          // Vérification d'une période (m)m/aaaa
          case "alnum_":
            if (!alnum_($champ)) {
              $msg = __("Alphanumérique non valide (majuscule et chiffre uniquement) !");
              return false;
            }
            break;

          // Vérification d'une adressse de stockage
          case "alnum_stk_":
            if (!alnum_stk_($champ)) {
              $msg = __("Alphanumérique non valide (lettre et chiffre uniquement) !");
              return false;
            }
            break;

          default:
            if (!ereg_OK($champ, $type)) {
              $msg = __("Champ non valide ! (<i>%s attendu</i>)", $type);
              return false;
            }
            break;

        }
      }
    }

    return true;
  }
}

if(!function_exists('remove_accent')) {
  function remove_accent($str)
  {
    return strtr($str, "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ", "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
  }
}

if(!function_exists('oraText')) {
  function oraText($phpText, $html = false, $like = false)
  {
    if ($like) $phpText = str_replace("*", "%", $phpText);

    if (!$html) $phpText = preg_replace('/[<>"]*/', '', $phpText);

    return str_replace("'", "''", $phpText);
  }
}

if(!function_exists('oraBool')) {
  function oraBool($phpBool)
  {
    return ($phpBool ? "1" : "0");
  }
}

if(!function_exists('phpFloat')) {
  function phpFloat($oraFloat)
  {
    return (float) strtr($oraFloat, array(',' => '.', ' ' => ''));
  }
}

if(!function_exists('oraFloat')) {
  function oraFloat($phpFloat, $keep_null = false)
  {
    global $_USER;

    $NLS_NUMERIC_CHARACTERS  = isset($_USER->langs['NLS_NUMERIC_CHARACTERS']) ? $_USER->langs['NLS_NUMERIC_CHARACTERS'] : ', ';

    if ($keep_null)
      return $phpFloat == null ? 'null' : phpFloat($phpFloat);
    else
      return str_replace(".", $NLS_NUMERIC_CHARACTERS{0}, phpFloat($phpFloat));
  }
}

if(!function_exists('oraInt')) {
  function oraInt($phpNum, $keep_null = false)
  {
    return $phpNum == null && $keep_null ? 'null' : (int)$phpNum;
  }
}

if(!function_exists('__')) {
  function __()
  {
    $needle = '%s';
    $arg_lists = func_get_args();
    if (count($arg_lists) == 0) return '';

    $txt_trad = _($arg_lists[0]);
    if (count($arg_lists) == 1) return $txt_trad;

    for ($i = 1; $i < count($arg_lists); $i++)
      $txt_trad = preg_replace('/%s/', $arg_lists[$i], $txt_trad, 1);

    return $txt_trad;
  }
}

if(!function_exists('showIco')) {
  function showIco($img, $text = '', $title = '', $padding = 3, $option = "")
  {
    if ($title == '')
      $title  =  $text;

    return '<img src="'. getPathImg($img) .'" alt="ico"'. ($title != '' ? ' title="'. $title .'"' : '') .' class="ico"'. ($option != '' ? ' '. $option : '') .'>'. ($text != '' ? '<span class="ico"'. ($padding != 3 ? ' style="padding-left: '. $padding .'px;"' : '') .'>'. $text .'</span>' : '');
  }
}

if(!function_exists('email_OK')) {
  function email_OK($email){
    return EMailAddress::verifyEmailAddress($email, $regs, true);
  }
}

if(!function_exists('emails_OK')) {
  function emails_OK($emails){
    foreach (EMail::emailSplit($emails) as $email)
      if (!email_OK($email))
        return false;

    return true;
  }
}

if(!function_exists('tel_OK')) {
  // Vérifie un téléphone
  function tel_OK($tel){
    return preg_match('/^\+{0,1}[0-9]+$/', $tel);
  }
}

if(!function_exists('int_OK')) {
  // Vérifie un int
  function int_OK($num){
    return preg_match('/^[0-9]+$/', $num);
  }
}

if(!function_exists('caract_OK')) {
  // Vérifie une chaine de caractères
  function caract_OK($num)
  {
    return preg_match('/^[a-zA-Z]+$/', $num);
  }
}

if(!function_exists('float_OK')) {
  // Vérifie une chaine de caractères
  function float_OK($num){
    return preg_match('/^[a-zA-Z]+$/', $num);
  }
}

if(!function_exists('float_OK')) {
  // Vérifie un float
  function float_OK($num){
    return (preg_match('/^[0-9]+[,\.]{1}[0-9]+$/', $num) || preg_match('/^[0-9]+$/', $num) || preg_match('/^[,\.]{1}[0-9]+$/', $num) || preg_match('/^[0-9]+[,\.]{1}$/', $num));
  }
}

if(!function_exists('float_neg_OK')) {
// Vérifie un float positif ou négatif
function float_neg_OK($num){
  return (preg_match('/^[-]{0,1}[0-9]+[,\.]{1}[0-9]+$/', $num) || preg_match('/^[-]{0,1}[0-9]+$/', $num) || preg_match('/^[-]{0,1}[,\.]{1}[0-9]+$/', $num) || preg_match('/^[-]{0,1}[0-9]+[,\.]{1}$/', $num));
}
}

if(!function_exists('cur_10_2_OK')) {
  // Vérification d'un tarif (10, 2)
  function cur_10_2_OK($num){
    return (preg_match('/^[0-9]{1,7}[,\.]{1}[0-9]{1,2}$/', $num) || preg_match('/^[0-9]{1,7}$/', $num) || preg_match('/^[,\.]{1}[0-9]{1,2}$/', $num) || preg_match('/^[0-9]{1,7}[,\.]{1}$/', $num));
  }
}

if(!function_exists('cur_11_3_OK')) {
  // Vérification d'un tarif (11, 3)
  function cur_11_3_OK($num){
    return (preg_match('/^[0-9]{1,8}[,\.]{1}[0-9]{1,3}$/', $num) || preg_match('/^[0-9]{1,8}$/', $num) || preg_match('/^[,\.]{1}[0-9]{1,3}$/', $num) || preg_match('/^[0-9]{1,8}[,\.]{1}$/', $num));
  }
}

if(!function_exists('cur_11_3_neg_OK')) {
  // Vérification d'un tarif (11, 3) positif ou négatif
  function cur_11_3_neg_OK($num){
    return (preg_match('/^[-]{0,1}[0-9]{1,8}[,\.]{1}[0-9]{1,3}$/', $num) || preg_match('/^[-]{0,1}[0-9]{1,8}$/', $num) || preg_match('/^[-]{0,1}[,\.]{1}[0-9]{1,3}$/', $num) || preg_match('/^[-]{0,1}[0-9]{1,8}[,\.]{1}$/', $num));
  }
}

if(!function_exists('cur_13_3_OK')) {
  // Vérification d'un tarif (13, 3)
  function cur_13_3_OK($num){
    return (preg_match('/^[0-9]{1,10}[,\.]{1}[0-9]{1,3}$/', $num) || preg_match('/^[0-9]{1,10}$/', $num) || preg_match('/^[,\.]{1}[0-9]{1,3}$/', $num) || preg_match('/^[0-9]{1,10}[,\.]{1}$/', $num));
  }
}

if(!function_exists('cur_15_3_OK')) {
  // Vérification d'un tarif (15, 3)
  function cur_15_3_OK($num){
    return (preg_match('/^[0-9]{1,8}[,\.]{1}[0-9]{1,3}$/', $num) || preg_match('/^[0-9]{1,12}$/', $num) || preg_match('/^[,\.]{1}[0-9]{1,3}$/', $num) || preg_match('/^[0-9]{1,12}[,\.]{1}$/', $num));
  }
}

if(!function_exists('cur_13_5_OK')) {
  // Vérification d'un tarif (13, 5) : stock et inventaire
  function cur_13_5_OK($num){
    return (preg_match('/^[0-9]{1,8}[,\.]{1}[0-9]{1,5}$/', $num) || preg_match('/^[0-9]{1,8}$/', $num) || preg_match('/^[,\.]{1}[0-9]{1,5}$/', $num) || preg_match('/^[0-9]{1,12}[,\.]{1}$/', $num));
  }
}

if(!function_exists('date_normal_OK')) {
  // Vérification d'une date (j)j/(m)m/aaaa
  function date_normal_OK($date){
    global $_USER;
    $format = ($_USER == null ? '%d/%m/%Y' : $_USER->langs['PHP_DATE_FORMAT']);

    /* Validation format */
    $reg = str_replace('%d', '([0-9]{1,2})', $format);
    $reg = str_replace('%m', '([0-9]{1,2})', $reg);
    $reg = str_replace('%Y', '([0-9]{4})', $reg);
    $reg = str_replace('/', '\/', $reg);
    $reg = '/^'. $reg .'$/';

    if (!preg_match($reg, $date, $regs)) return false;

    /* Validation date */
    $parms  = array(strpos($format, '%d') => 'j', strpos($format, '%m') => 'm', strpos($format, '%Y') => 'a');
    ksort($parms);   $parms = array_values($parms);

    return checkdate($regs[array_search('m', $parms) + 1], $regs[array_search('j', $parms) + 1], $regs[array_search('a', $parms) + 1]);
  }
}

if(!function_exists('date_periode_OK')) {
  function date_periode_OK($periode){
    global $_USER;
    $format = ($_USER == null ? '%m/%Y' : $_USER->langs['PHP_PERIODE_FORMAT']);

    /* Validation format */
    $reg = str_replace('%m', '([0-9]{1,2})', $format);
    $reg = str_replace('%Y', '([0-9]{4})', $reg);
    $reg = str_replace('/', '\/', $reg);
    $reg = '/^'. $reg .'$/';

    if (!preg_match($reg, $periode, $regs)) return false;

    /* Validation période */
    $parms  = array(strpos($format, '%m') => 'm', strpos($format, '%Y') => 'a');
    ksort($parms);   $parms = array_values($parms);

    return checkdate($regs[array_search('m', $parms) + 1], 1, $regs[array_search('a', $parms) + 1]);
  }
}

if(!function_exists('date_week_year_OK')) {
  function date_week_year_OK($periode){
    global $_USER;
    $format = ($_USER == null ? '%V/%G' : $_USER->langs['PHP_WEEK_YEAR_FORMAT']);

    /* Validation format */
    $reg = str_replace('%V', '([0-9]{1,2})', $format);
    $reg = str_replace('%G', '([0-9]{4})', $reg);
    $reg = str_replace('/', '\/', $reg);
    $reg = '/^'. $reg .'$/';

    if (!preg_match($reg, $periode, $regs)) return false;

    /* Validation période */
    return true;
  }
}

if(!function_exists('heure_normal_OK')) {
  // Vérification d'une heure HH:MM
  function heure_normal_OK($heure){
    global $_USER;
    $format = ($_USER == null ? '%H:%M' : $_USER->langs['PHP_TIME_FORMAT']);

    /* Validation format */
    $reg = str_replace('%H', '([0-9]{1,2})', $format);
    $reg = str_replace('%M', '([0-9]{1,2})', $reg);
    $reg = '/^'. $reg .'$/';

    if (!preg_match($reg, $heure, $regs)) return false;

    /* Validation heure */
    $parms  = array(strpos($format, '%H') => 'H', strpos($format, '%M') => 'M');
    ksort($parms);   $parms = array_values($parms);

    $h = $regs[array_search('H', $parms) + 1];
    $m = $regs[array_search('M', $parms) + 1];

    return ($h >= 0 && $h < 24 && $m >= 0 && $m < 60);
  }
}

if(!function_exists('heure_minute_OK')) {
  // Vérification d'une heure HH:MM:SS
  function heure_minute_OK($heure){
    if (!preg_match('/^([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $heure, $regs)) return false;

    $h = $regs[1];  $m = $regs[2];  $s = $regs[3];

    return ($h >= 0 && $h < 24 && $m >= 0 && $m < 60 && $s >= 0 && $s < 60);
  }
}

if(!function_exists('alnum_')) {
  function alnum_($num){
    return preg_match('/^[A-Z0-9_]+$/', $num);
  }
}

if(!function_exists('alnum_stk_')) {
  function alnum_stk_($num){
    return preg_match('/^[-a-zA-Z0-9_ ]+$/', $num);
  }
}

if(!function_exists('ereg_OK')) {
  // Vérifie l'expression reg
  function ereg_OK($champ, $ereg){
    if (!preg_match('#/([^/]*)/#', $ereg)) {
      $ereg = sprintf('/%s/', $ereg);
    }
    return preg_match($ereg, $champ);
  }
}

if(!function_exists('formIsSubmit')) {
  function formIsSubmit($nom)
  {
    return post($nom, "0") == "1";
  }
}

if(!function_exists('post')) {
  function post($var, $default = "")
  {
    return isset($_POST[$var]) ? $_POST[$var] : $default;
  }
}

if(!function_exists('postInt')) {
  function postInt($var, $default = "")
  {
    return (int)post($var, $default);
  }
}


