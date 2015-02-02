<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.0
 * @copyright    Copyright (C) 2015 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

class EtdOptimizerHelper {

    /**
     * @var array Paramètres du plugin.
     */
    protected $params;

    /**
     * @var string Chemin de base vers le plugin.
     */
    protected $basePath;

    /**
     * @var string Chemin de base vers le plugin.
     */
    protected $vendorURI;

    /**
     * @var string
     */
    protected $rootURI;

    /**
     * @var string
     */
    protected $templatePath;

    /**
     * @var string
     */
    protected $templateUri;

    /**
     * @var string
     */
    protected $charset;

    /**
     * @var string
     */
    protected $docTitle;

    /**
     * @var string
     */
    protected $docDescription;

    /**
     * @var string Les mots clés dans le HEAD
     */
    protected $docKeywords;

    /**
     * @var array
     */
    protected $docCustom;

    /**
     * @var array
     */
    protected $docLinks;

    /**
     * @var array
     */
    protected $docStylesheets;

    /**
     * @var array
     */
    protected $docStyles;

    /**
     * @var array
     */
    protected $docScripts;

    /**
     * @var array
     */
    protected $docScript;

    function __construct($params, $basePath, $vendorURI, $rootURI, $templatePath, $templateUri) {

         $this->params = $params;
         $this->basePath = rtrim($basePath,"/")."/";
         $this->vendorURI = rtrim($vendorURI,"/")."/";
         $this->rootURI = rtrim($rootURI,"/")."/";
         $this->templatePath = rtrim($templatePath,"/")."/";
         $this->templateUri = rtrim($templateUri,"/")."/";
     }

    /**
     * @return array
     */
    public function getParams() {

        return $this->params;
    }

    /**
     * @return mixed
     */
    public function getParam($param) {

        return array_key_exists($param, $this->params) ? $this->params[$param] : null;
    }

    /**
     * @param array $params
     */
    public function setParams($params) {

        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getBasePath() {

        return $this->basePath;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath($basePath) {

        $this->basePath = $basePath;
    }

    public function setTemplatePath($path) {

        $this->templatePath = $path;
    }

    public function getTemplatePath() {

        return $this->templatePath;
    }

    public function setTemplateUri($uri) {

        $this->templateUri = $uri;
    }

    public function getTemplateUri() {

        return $this->templateUri;
    }

    /**
     * @return mixed
     */
    public function getCharset() {

        return $this->charset;
    }

    /**
     * @return mixed
     */
    public function getDocTitle() {

        return $this->docTitle;
    }

    /**
     * @param mixed $doc_title
     */
    public function setDocTitle($doc_title) {

        $this->docTitle = $doc_title;
    }

    /**
     * @param mixed $charset
     */
    public function setCharset($charset) {

        $this->charset = $charset;
    }

    /**
     * @return mixed
     */
    public function getDocDescription() {

        return $this->docDescription;
    }

    /**
     * @param mixed $doc_description
     */
    public function setDocDescription($doc_description) {

        $this->docDescription = $doc_description;
    }

    public function hasDocDescription() {

        return !empty($this->doc_description);
    }

    /**
     * @return string
     */
    public function getDocKeywords() {

        return $this->docKeywords;
    }

    /**
     * @param string $doc_keywords
     */
    public function setDocKeywords($doc_keywords) {

        $this->docKeywords = $doc_keywords;
    }

    public function hasDocKeywords() {

        return !empty($this->docKeywords);
    }

    /**
     * @return array
     */
    public function getDocLinks() {

        return $this->docLinks;
    }

    /**
     * @param array $doc_links
     */
    public function setDocLinks($doc_links) {

        $this->docLinks = $doc_links;
    }

    public function hasDocLinks() {

        return !empty($this->docLinks);
    }

    /**
     * @return array
     */
    public function getDocStylesheets() {

        return $this->docStylesheets;
    }

    /**
     * @param array $doc_stylesheets
     */
    public function setDocStylesheets($doc_stylesheets) {

        $this->docStylesheets = $doc_stylesheets;
    }

    public function hasDocStylesheets() {

        return !empty($this->docStylesheets);
    }

    /**
     * @return array
     */
    public function getDocStyles() {

        return $this->docStyles;
    }

    /**
     * @param array $docStyles
     */
    public function setDocStyles($docStyles) {

        $this->docStyles = $docStyles;
    }

    public function hasDocStyles() {

        return !empty($this->docStyles);
    }

    /**
     * @return array
     */
    public function getDocScripts() {

        return $this->docScripts;
    }

    /**
     * @param array $docScripts
     */
    public function setDocScripts($docScripts) {

        $this->docScripts = $docScripts;
    }

    public function hasDocScripts() {

        return !empty($this->docScripts);
    }

    /**
     * @return array
     */
    public function getDocScript() {

        return $this->docScript;
    }

    /**
     * @param array $docScript
     */
    public function setDocScript($docScript) {

        $this->docScript = $docScript;
    }

    public function hasDocScript() {

        return !empty($this->docScript);
    }

    /**
     * @return string
     */
    public function getRootURI() {

        return $this->rootURI;
    }

    /**
     * @param string $rootURI
     */
    public function setRootURI($rootURI) {

        $this->rootURI = $rootURI;
    }

    /**
     * @return string
     */
    public function getVendorURI() {

        return $this->vendorURI;
    }

    /**
     * @param string $vendorURI
     */
    public function setVendorURI($vendorURI) {

        $this->vendorURI = $vendorURI;
    }

    public function hasDocCustom() {

        return !empty($this->docCustom);
    }

    /**
     * @return array
     */
    public function getDocCustom() {

        return $this->docCustom;
    }

    /**
     * @param array $docCustom
     */
    public function setDocCustom($docCustom) {

        $this->docCustom = $docCustom;
    }

    /**
     * Méthode pour mettre à jour les infos en bloc concernant le document.
     *
     * @param $charset
     * @param $docTitle
     * @param $docDescription
     * @param $docKeywords
     * @param $docLinks
     * @param $docStylesheets
     * @param $docStyles
     * @param $docScripts
     * @param $docScript
     * @param $docCustom
     */
    public function updateDoc($charset, $docTitle, $docDescription, $docKeywords, $docLinks, $docStylesheets, $docStyles, $docScripts, $docScript, $docCustom) {

        $this->charset = $charset;
        $this->docTitle = $docTitle;
        $this->docDescription = $docDescription;
        $this->docKeywords = $docKeywords;
        $this->docLinks = $docLinks;
        $this->docStylesheets = $docStylesheets;
        $this->docStyles = $docStyles;
        $this->docScripts = $docScripts;
        $this->docScript = $docScript;
        $this->docCustom = $docCustom;
    }

    public function isMobile($currentHost) {

        $mobileHost = parse_url($this->params[PARAM_MOBILE_URI])['host'];

        if (!class_exists('uagent_info')) {
            throw new RuntimeException(sprintf('Classe non trouvée : %s', 'uagent_info'), 500);
        }

        // On instancie le détecteur.
        $ua = new uagent_info();

        return (($ua->isMobilePhone || $ua->isTierIphone) || ($this->params[PARAM_MOBILE_TABLETS] && $ua->isTierTablet) || ($currentHost == $mobileHost));

    }

    public function replaceParts($content) {

        $files = EtdOptimizerHelper::getParts();
        if (!empty($files)) {

            // On initialise le tableau de remplacement.
            $replace = array();

            foreach ($files as $file) {
                $name = strtolower(preg_replace('#\.[^.]*$#', '', $file));

                // On vérifie que l'on a besoin de bosser.
                if (strpos($content, '<etdoptimizer:' . $name) === false) {
                    break;
                }

                $instance = self::getPart($name);
                if ($instance) {
                    $replace['/<etdoptimizer:' . $name . '\s*?\/>/i'] = $instance->render();
                } else {
                    $replace['/<etdoptimizer:' . $name . '\s*?\/>/i'] = '<!-- impossible de trouver la partie ' . $name . ' -->';
                }
            }

            // On remplace les occurences.
            $content = preg_replace(array_keys($replace), $replace, $content);

        }

        // On remplace les balises non gérées.
        $content = preg_replace('/<etdoptimizer:[a-z]*?(\s*)?\/>/i', '', $content);

        return $content;

    }

    public function getParts() {

        return glob($this->basePath . "/library/parts/*.php");

    }

    public function getPart($type) {

        $type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
        $partClass = 'EtdOptimizer' . ucfirst($type);

        if (!class_exists($partClass)) {
            throw new RuntimeException(sprintf('Classe part non trouvée : %s', $partClass), 500);
        }

        return new $partClass($this);
    }

    /**
     * Utility function to map an array to a string.
     *
     * @param   array $array The array to map.
     * @param   string $inner_glue The glue (optional, defaults to '=') between the key and the value.
     * @param   string $outer_glue The glue (optional, defaults to ' ') between array elements.
     * @param   boolean $keepOuterKey True if final key should be kept.
     *
     * @return  string   The string mapped from the given array
     *
     * @since   11.1
     */
    public static function ArraytoString($array = null, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey = false) {

        $output = array();

        if (is_array($array)) {
            foreach ($array as $key => $item) {
                if (is_array($item)) {
                    if ($keepOuterKey) {
                        $output[] = $key;
                    }
                    // This is value is an array, go and do it again!
                    $output[] = EtdOptimizerHelper::ArraytoString($item, $inner_glue, $outer_glue, $keepOuterKey);
                } else {
                    $output[] = $key . $inner_glue . '"' . $item . '"';
                }
            }
        }

        return implode($outer_glue, $output);
    }

}