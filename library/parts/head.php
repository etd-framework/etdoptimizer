<?php

/**
 * @package      ETD Optimizer
 *
 * @version      2.6.0
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

class EtdOptimizerHead extends EtdOptimizerPart {

    protected $addLoadCSS = false;
	
	public function render() {

		$template_path = $this->helper->getTemplatePath();
		$template_uri  = $this->helper->getTemplateUri();

		ob_start();

		echo "<meta charset=\"" . $this->helper->getCharset() . "\">\n";
		echo "<title>" . htmlspecialchars($this->helper->getDocTitle(), ENT_COMPAT, $this->helper->getCharset()) . "</title>\n";

		$docDescription = $this->helper->getDocDescription();
		if (!empty($docDescription)) {
			echo "<meta name=\"description\" content=\"" . htmlspecialchars($docDescription, ENT_COMPAT, $this->helper->getCharset()) . "\">\n";
		}

		$docKeywords = $this->helper->getDocKeywords();
		if (!empty($docKeywords)) {
			echo "<meta name=\"keywords\" content=\"" . htmlspecialchars($docKeywords, ENT_COMPAT, $this->helper->getCharset()) . "\">\n";
		}

        $viewport = $this->helper->getParam(PARAM_VIEWPORT);
        if (!empty($viewport)) {
            echo "<meta name=\"viewport\" content=\"" . $viewport . "\">\n";
        }

		$docMeta = $this->helper->getDocMeta();
		if (!empty($docMeta)) {
			foreach ($docMeta as $name => $content) {
				$type = "name";

				// Open Graph (FB) ?
				if (strpos($name, 'og:') === 0 || strpos($name, 'fb:') === 0 || strpos($name, 'product:') === 0) {
					$type = "property";
				}

				echo "<meta ".$type."=\"" . $name . "\" content=\"" . htmlspecialchars($content, ENT_COMPAT, $this->helper->getCharset()) . "\">\n";
			}
		}

		$docLinks = $this->helper->getDocLinks();
		if (!empty($docLinks)) {
			foreach ($docLinks as $link => $linkAtrr) {
				$attribs = '';
				if (!empty($linkAtrr['attribs'])) {
					$attribs = ' ' . EtdOptimizerHelper::ArraytoString($linkAtrr['attribs']);
				}
				echo "<link href=\"" . $link . "\" " . $linkAtrr['relType'] . "=\"" . $linkAtrr['relation'] . "\"" . $attribs . ">\n";
			}
		}

		// On traite les polices Google.
		$fonts = $this->helper->getParam(PARAM_GOOGLE_FONTS);
		if (!empty($fonts)) {
			echo "<link rel=\"stylesheet\" href=\"http://fonts.googleapis.com/css?family=" . trim($fonts) . "\">\n";
		}

		// On traite les feuilles de styles.
		$docStylesheets = $this->helper->getDocStylesheets();
		if (!empty($docStylesheets)) {

			$css_exclude = explode(',', $this->helper->getParam(PARAM_CSS_EXCLUDE));
			$css_exclude = array_map('trim', $css_exclude);

			foreach($docStylesheets as $source => $attribs) {

                // Toutes sauf celles qui vont en bas de page.
                if (!isset($attribs['attribs']) || is_array($attribs['attribs']) && !isset($attribs['attribs']['bottom']) || is_array($attribs['attribs']) && isset($attribs['attribs']['bottom']) && !$attribs['attribs']['bottom']) {

                    // On récupère le chemin et nom de fichier depuis l'URL.
                    $path = str_replace($this->helper->getRootURI(), '/', $source);
                    $file = substr($source, strrpos($source, '/') + 1);

                    // On retire les fichiers exclus.
                    if (in_array($file, $css_exclude) || in_array($path, $css_exclude)) {
                        continue;
                    }

                    // On remplace les scripts des modules par ceux du template s'ils existent.
                    if (strpos($source, 'modules/') !== false || strpos($source, 'media/jui/css/') !== false || strpos($source, 'media/system/css/') !== false) {

                        $min_css_path = $template_path . 'custom' . str_replace('.css', '.min.css', $path);
                        $css_path = $template_path . 'custom' . $path;

                        // On regarde si une version minimisée existe.
                        if (file_exists($min_css_path)) {
                            $source = $template_uri . 'custom' . str_replace('.css', '.min.css', $path);
                        } elseif (file_exists($css_path)) { // On regarde pour la version normale.
                            $source = $template_uri . 'custom' . $path;
                        }

                    }

                    // Preload
                    if (isset($attribs['attribs']) && isset($attribs['attribs']['preload']) && $attribs['attribs']['preload']) {
                        echo "<link rel=\"preload\" href=\"" . $source . "\" as=\"style\" onload=\"this.rel='stylesheet'\">\n";
                        echo "<noscript><link rel=\"stylesheet\" href=\"" . $source . "\"></noscript>\n";
                        $this->addLoadCSS = true;
                    } else {
                        echo "<link rel=\"stylesheet\" href=\"" . $source . "\">\n";
                    }

                }

			}

		}

		$docStyles = $this->helper->getDocStyles();
		if (!empty($docStyles)) {
			foreach ($docStyles as $type => $content) {
				if (!empty($content)) {
					if ($this->helper->getParam(PARAM_MINIFY)) {
						$minifier = new MatthiasMullie\Minify\CSS($content);
						$content = $minifier->minify();
					}
					echo "<style type=\"" . $type . "\">\n" . $content . "</style>\n";
				}
			}
		}

		// Inline loadCSS
		if ($this->addLoadCSS) {
            echo "<script>
!function(a){\"use strict\";var b=function(b,c,d){function e(a){return h.body?a():void setTimeout(function(){e(a)})}function f(){i.addEventListener&&i.removeEventListener(\"load\",f),i.media=d||\"all\"}var g,h=a.document,i=h.createElement(\"link\");if(c)g=c;else{var j=(h.body||h.getElementsByTagName(\"head\")[0]).childNodes;g=j[j.length-1]}var k=h.styleSheets;i.rel=\"stylesheet\",i.href=b,i.media=\"only x\",e(function(){g.parentNode.insertBefore(i,c?g:g.nextSibling)});var l=function(a){for(var b=i.href,c=k.length;c--;)if(k[c].href===b)return a();setTimeout(function(){l(a)})};return i.addEventListener&&i.addEventListener(\"load\",f),i.onloadcssdefined=l,l(f),i};\"undefined\"!=typeof exports?exports.loadCSS=b:a.loadCSS=b}(\"undefined\"!=typeof global?global:this);
!function(a){if(a.loadCSS){var b=loadCSS.relpreload={};if(b.support=function(){try{return a.document.createElement(\"link\").relList.supports(\"preload\")}catch(b){return!1}},b.poly=function(){for(var b=a.document.getElementsByTagName(\"link\"),c=0;c<b.length;c++){var d=b[c];\"preload\"===d.rel&&\"style\"===d.getAttribute(\"as\")&&(a.loadCSS(d.href,d,d.getAttribute(\"media\")),d.rel=null)}},!b.support()){b.poly();var c=a.setInterval(b.poly,300);a.addEventListener&&a.addEventListener(\"load\",function(){b.poly(),a.clearInterval(c)}),a.attachEvent&&a.attachEvent(\"onload\",function(){a.clearInterval(c)})}}}(this);
</script>";
        }

		if ($this->helper->getParam(PARAM_MODERNIZR) && !$this->helper->getParam(PARAM_IS_MOBILE)) {
			echo "<script src=\"" . $this->helper->getVendorURI() . "etdsolutions/modernizr/modernizr.min.js\"></script>";
		}

		$docCustom = $this->helper->getDocCustom();
		if (!empty($docCustom)) {
			echo "\n";
			foreach ($docCustom as $custom)  {
				echo $custom . "\n";
			}
		}

		return ob_get_clean();
		
	}
	
}
