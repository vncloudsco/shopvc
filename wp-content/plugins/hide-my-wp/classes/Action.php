<?php

/**
 * Set the ajax action and call for wordpress
 */
class HMW_Classes_Action extends HMW_Classes_FrontController {

    /** @var array with all form and ajax actions */
    var $actions = array();

    /** @var array from core config */
    private static $config;


    public function isAjax() {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return true;
        }
        return false;
    }

    /**
     * The hookAjax is loaded as custom hook in hookController class
     *
     * @return void
     */
    function hookInit() {
        /* Only if ajax */
        if ($this->isAjax()) {
            $this->getActions();
        }
    }


    /**
     * The hookSubmit is loaded when action si posted
     *
     * @return void
     */
    function hookMenu() {

        /* Only if post */
        if (!$this->isAjax()) {
            $this->getActions();
        }
    }

    /**
     * Load for frontend users with ajax calls
     */
    function hookFrontinit() {
        /* Only if post */
        if (!$this->isAjax()) {
            $this->getActions();
        }
    }


    /**
     * Get all actions from config.json in core directory and add them in the WP
     *
     */
    public function getActions() {
        $this->actions = array();
        $cur_action = HMW_Classes_Tools::getValue('action');

        /* if config allready in cache */
        if (!isset(self::$config)) {
            $config_file = _HMW_ROOT_DIR_ . '/config.json';
            if (!file_exists($config_file))
                return;

            /* load configuration blocks data from core config files */
            self::$config = json_decode(file_get_contents($config_file), 1);
        }

        if (is_array(self::$config))
            foreach (self::$config['blocks']['block'] as $block) {
                if (isset($block['active']) && $block['active'] == 1)
                    if (isset($block['admin']) &&
                        (($block['admin'] == 1 && (is_admin() || is_network_admin())) ||
                            $block['admin'] == 0)
                    ) {
                        /* if there is a single action */
                        if (isset($block['actions']['action']))

                            /* if there are more actions for the current block */
                            if (!is_array($block['actions']['action'])) {
                                /* add the action in the actions array */
                                if ($block['actions']['action'] == $cur_action)
                                    $this->actions[] = array('class' => $block['name']);
                            } else {
                                /* if there are more actions for the current block */
                                foreach ($block['actions']['action'] as $action) {
                                    /* add the actions in the actions array */
                                    if ($action == $cur_action)
                                        $this->actions[] = array('class' => $block['name']);
                                }
                            }
                    }
            }

        /* add the actions in WP */
        foreach ($this->actions as $actions) {
            HMW_Classes_ObjController::getClass($actions['class'])->action();
        }
    }

}
