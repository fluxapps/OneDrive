<?php

namespace srag\Plugins\OneDrive\Input;

use ilFormPropertyGUI;
use ILIAS\DI\Container;
use ilPlugin;
use ilTemplate;
use ilUtil;
use ilPropertyFormGUI;
use srag\CustomInputGUIs\OneDrive\Waiter\Waiter;
use ilGlobalPageTemplate;

/**
 * Class srChunkedDirectFileUploadInputGUI
 *
 * This file input executes a chunked upload directly to an external url.
 * It was designed for the OneDrive plugin and therefore for the OneDrive API. It should work with other
 * chunk-upload-compatible APIs, though it might have to be adjusted a little. Also it does not yet work
 * with other input fields in the same form.
 *
 * Workflow:
 * - fetch target upload url via $url_fetch_upload_url (direct chunk-upload-compatible API url with no authentication)
 * - upload files in chunks (PUT request with Content-Range Header)
 * - on fail: POST $upload_failed_url with filename
 * - on abort: POST $upload_aborted_url with filename
 * - on complete: execute $after_upload_js_callback
 *
 * @package srag\DIC\OneDrive\Plugin
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class srChunkedDirectFileUploadInputGUI extends ilFormPropertyGUI
{
    const DEFAULT_CHUNK_SIZE = 327680 * 40;
    /**
     * @var string
     */
    protected $submit_cmd;
    /**
     * @var ilTemplate
     */
    protected $tpl;

    /**
     * @var ilPropertyFormGUI
     */
    protected $form;
    /**
     * @var ilPlugin
     */
    protected $plugin;
    /**
     * @var int
     */
    protected $chunk_size = self::DEFAULT_CHUNK_SIZE;
    /**
     * @var string
     * used to fetch target url
     */
    protected $url_fetch_upload_url;
    /**
     * @var string
     * javascript callable
     */
    protected $after_upload_js_callback;
    /**
     * @var string
     */
    protected $upload_failed_url;
    /**
     * @var string
     */
    protected $upload_aborted_url;

    protected static $js_loaded = false;

    /**
     * @var Container
     */
    protected $dic;

    public function __construct(ilPropertyFormGUI $ilPropertyFormGUI, ilPlugin $plugin, string $url_fetch_upload_url, string $submit_cmd, $a_title = "")
    {
        global $DIC;
        $this->dic = $DIC;
        $this->plugin = $plugin;
        $this->form = $ilPropertyFormGUI;
        $this->url_fetch_upload_url = $url_fetch_upload_url;
        $this->loadJavaScript($DIC->ui()->mainTemplate());
        $this->tpl = new ilTemplate(__DIR__ . '/html/tpl.chunked_upload.html', true, true);
        parent::__construct($a_title, "");
        $this->submit_cmd = $submit_cmd;
    }

    /**
     * @param ilTemplate|ilGlobalPageTemplate $a_tpl
     * @param bool $force
     */
    public static function loadJavaScript($a_tpl, bool $force = false)
    {
        if (!self::$js_loaded || $force) {
            Waiter::init(Waiter::TYPE_PERCENTAGE, $a_tpl);
            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);
            $a_tpl->addJavaScript($dir . '../../../node_modules/blueimp-file-upload/js/jquery.fileupload.js');
            $a_tpl->addJavaScript($dir .  '/js/ChunkedUpload.min.js');
            self::$js_loaded = true;
        }
    }

    protected function getSessionRefreshUrl()
    {
        return ILIAS_HTTP_PATH;
    }

    /**
     * @param string $input_id
     * @return string
     */
    protected function getOnloadCode(string $input_id) : string
    {
        return 'initChunkedUpload(' .
            '"' . $input_id . '",' .
            '"' . $this->form->getId() . '",' .
            '"' . $this->url_fetch_upload_url . '",' .
            '' . $this->chunk_size . ',' .
            '"' . $this->getAfterUploadJsCallback() . '",' .
            '"' . $this->getUploadAbortedUrl() . '",' .
            '"' . $this->getUploadFailedUrl() . '",' .
            '"' . $this->getSessionRefreshUrl() . '",' .
            '"' . ($this->getRequired() ? $this->submit_cmd : "") . '")';
    }

    public function render() : string
    {
        $tmp_id = ilUtil::randomhash();
        $this->tpl->setVariable('LABEL', $this->dic->language()->txt('select_file'));
        $this->tpl->setVariable('ID', $tmp_id);
        return $this->tpl->get() .
            '<script type="text/javascript">' . $this->getOnloadCode($tmp_id) . '</script>';
    }

    public function insert($a_tpl)
    {
        $html = $this->render();
        $this->loadJavaScript($a_tpl);

        $a_tpl->setCurrentBlock("prop_generic");
        $a_tpl->setVariable("PROP_GENERIC", $html);
        $a_tpl->parseCurrentBlock();
    }

    /**
     * @return int
     */
    public function getChunkSize() : int
    {
        return $this->chunk_size;
    }

    /**
     * @param int $chunk_size
     */
    public function setChunkSize(int $chunk_size)
    {
        $this->chunk_size = $chunk_size;
    }

    /**
     * @return string
     */
    public function getUrlFetchUploadUrl() : string
    {
        return $this->url_fetch_upload_url;
    }

    /**
     * @param string $url_fetch_upload_url
     */
    public function setUrlFetchUploadUrl(string $url_fetch_upload_url)
    {
        $this->url_fetch_upload_url = $url_fetch_upload_url;
    }

    /**
     * @return string
     */
    public function getAfterUploadJsCallback() : string
    {
        return $this->after_upload_js_callback;
    }

    /**
     * @param string $after_upload_js_callback
     */
    public function setAfterUploadJsCallback(string $after_upload_js_callback)
    {
        $this->after_upload_js_callback = $after_upload_js_callback;
    }

    /**
     * @return string
     */
    public function getUploadFailedUrl() : string
    {
        return $this->upload_failed_url;
    }

    /**
     * @param string $upload_failed_url
     */
    public function setUploadFailedUrl(string $upload_failed_url)
    {
        $this->upload_failed_url = $upload_failed_url;
    }

    /**
     * @return string
     */
    public function getUploadAbortedUrl() : string
    {
        return $this->upload_aborted_url;
    }

    /**
     * @param string $upload_aborted_url
     */
    public function setUploadAbortedUrl(string $upload_aborted_url)
    {
        $this->upload_aborted_url = $upload_aborted_url;
    }

}
