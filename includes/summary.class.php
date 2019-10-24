<?php

/*
 * https://carlalexander.ca/designing-class-generate-wordpress-html-content/
 */

class Patterns_SummaryGenerator
{
    /**
     * Path to the default template used by the highlighted comment generator.
     *
     * @var string
     */
    private $default_template_path;
 
    /**
     * Name of the filter used to filter the template path.
     *
     * @var string
     */
    private $filter_name;
 
    /**
     * Template name used by the `get_query_template` function.
     *
     * @var string
     */
    private $query_template_name;
 
    /**
     * Constructor.
     *
     * @param string $default_template_path
     * @param string $filter_name
     * @param string $query_template_name
     */
    public function __construct($default_template_path, $filter_name, $query_template_name)
    {
        $this->default_template_path = $default_template_path;
        $this->filter_name = $filter_name;
        $this->query_template_name = $query_template_name;
    }
 
    /**
     * Generates the highlighted comment HTML for the given comment.
     *
     * @param WP_Comment $comment
     *
     * @return string
     */
    public function generate($group)
    {
        $template_path = $this->get_template_path();
 
        if (!is_readable($template_path)) {
            return sprintf('<!-- Could not read "%s" file -->', $template_path);
        }
 
        ob_start();
 
        include $template_path;
 
        return ob_get_clean();
    }
 
    /**
     * Get the path of PHP template that the comment generator will use.
     *
     * @return string
     */
    private function get_template_path()
    {
        $template_path = get_query_template($this->query_template_name);
 
        if (empty($template_path)) {
            $template_path = $this->default_template_path;
        }
 
        return apply_filters($this->filter_name, $template_path);
    }
}
