<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Theme_hattrick_mg extends Theme
{
    public $name			= 'Hattrick-MG Twitter Bootstrap';
    public $author			= 'willaser';
    public $author_website	= 'http://stevemo.ca/';
    public $website			= 'http://twitter.github.com/bootstrap/index.html';
    public $description		= 'Twitter bootstrap adapted to Hattrick-MG.';
    public $version			= '1.21.017';
    public $options 		= array(
        'show_breadcrumbs' => 	array('title' 		=> 'Show Breadcrumbs',
        'description'   => 'Would you like to display breadcrumbs?',
        'default'       => 'no',
        'type'          => 'radio',
        'options'       => 'yes=Yes|no=No',
        'is_required'   => TRUE),
    );
}

/* End of file theme.php */