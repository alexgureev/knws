<?php namespace Knws;

class TemplateManager
{
    public function render($template, $content)
    {
        // Render output with remplate and content array
        return RPC::Twig(array('ns' => '\\'))->render($template.'.twig', $content);
    }

}
?>
