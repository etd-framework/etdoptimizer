<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.6.5
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

abstract class Controller extends ControllerCore {

    /**
     * Renders controller templates and generates page content
     *
     * @param array|string $content Template file(s) to be rendered
     * @throws Exception
     * @throws SmartyException
     */
    protected function smartyOutputContent($content) {

        // On met l'output dans le buffer pour le modifier avec le plugin.
        ob_start();

        // Seulement pour les versions < 1.6
        if (version_compare(_PS_VERSION_, '1.6', '<')) {

            $this->context->cookie->write();

            $html   = '';
            $js_tag = 'js_def';

            $this->context->smarty->assign($js_tag, $js_tag);

            if ($this->controller_type == 'front' && $this->getLayout())  {

                $this->context->smarty->assign(array(
                    $js_tag     => Media::getJsDef(),
                    'js_files'  => array_unique($this->js_files),
                    'js_inline' => Media::getInlineScript()
                ));

            }

            if (is_array($content)) {
                foreach ($content as $tpl) {
                    $html = $this->context->smarty->fetch($tpl);
                }
            } else {
                $html = $this->context->smarty->fetch($content);
            }

            $html = trim($html);

            echo $html;

        } else { // On appel le parent pour les versions > 1.6

            parent::smartyOutputContent($content);

        }

        // On récupère l'HTML généré par smarty.
        $html = ob_get_clean();

        // On vérifie que l'on a quelque chose à faire.
        if (strpos($html, '<etdoptimizer:') === false || !isset(EtdOptimizer::$helper)) {
            echo $html;
            return;
        }

        $css_files = array();
        $js_files  = array();
        $js_inline = array();
        if (array_key_exists('css_files', $this->context->smarty->tpl_vars)) {
            $css_files = $this->context->smarty->tpl_vars['css_files']->value;
        }
        $css_files = array_merge($css_files, EtdOptimizer::$stylesheets);
        if (array_key_exists('js_files', $this->context->smarty->tpl_vars)) {
            $js_files = $this->context->smarty->tpl_vars['js_files']->value;
        }
        $js_files = array_merge(array_flip($js_files), EtdOptimizer::$scripts);
        if (array_key_exists('js_inline', $this->context->smarty->tpl_vars)) {
            $js_inline = $this->context->smarty->tpl_vars['js_inline']->value;
        }
        $js_inline = array_merge($js_inline, EtdOptimizer::$js);

        // JS Def
        $js_def = Media::getJsDef();
        $c = count($js_def);
        if ($c) {
            $buffer = "var ";
            $i = 0;
            foreach ($js_def as $key => $value) {
                $i++;
                if (!empty($key)) {
                    $buffer .= $key ." = ";
                }
                $buffer .= json_encode($value);
                if ($i < $c) {
                    $buffer .= ",\n";
                }
            }
            $buffer .= ";\n";
            array_unshift($js_inline, $buffer);
        }

        EtdOptimizer::$helper->updateDoc(
            'utf-8',
            $this->context->smarty->tpl_vars['meta_title']->value,
            $this->context->smarty->tpl_vars['meta_description']->value,
            $this->context->smarty->tpl_vars['meta_keywords']->value,
            null,
            $css_files,
            EtdOptimizer::$css,
            $js_files,
            $js_inline,
            EtdOptimizer::$custom,
            array()
        );

        // On récupère toutes les parties gérées par le plugin.
        $html = EtdOptimizer::$helper->replaceParts($html);

        echo $html;

    }

}
