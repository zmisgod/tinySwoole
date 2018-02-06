<?php
namespace Core\Framework;

class HTMLTemplate
{
    static $fileName = [];

    protected $controllerName;

    protected $actionName;

    protected $data;

    protected $layout = '';
    protected $layout_data;

    protected $not_fond_template_dir = 'Public/404.html';

    public function __construct($controller_name, $action_name, $data = null)
    {
        $this->controllerName = $controller_name;

        $this->actionName = $action_name;

        if($data !== null) {
            $this->data = $data;
        }
    }

    public function display()
    {
        $template = ROOT.'App/View/'. strtolower($this->controllerName).'/'.strtolower($this->actionName).'.php';
        if(!file_exists($template)) {
            $template = ROOT.$this->not_fond_template_dir;
        }

        if($this->data !== null) {
            extract($this->data, EXTR_OVERWRITE);
        }

        ob_start();
        include $template;
        $content = ob_get_clean();

        return $content;
    }

    public function setLayout($template_path, $data = null)
    {
        $template = ROOT.$template_path;
        if(!file_exists($template)) {
            throw new \Exception("not found template layouts:".$template_path);
        }

        $this->layout = $template_path;
        $this->layout_data = $data;
    }
}