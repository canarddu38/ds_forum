<?php

if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

$plugins->add_hook("admin_tools_menu", "awardgranting_menu");
$plugins->add_hook("admin_load", "awardgranting_admin");
$plugins->add_hook("admin_tools_action_handler", "awardgranting_action_handler");
$plugins->add_hook("pre_output_page", "awardgranting_dogrant");

function awardgranting_info()
{
    return array(
        "name"          => "Automatic Award Granting",
        "description"   => "Auto award granting to members",
        "website"       => "http://www.elegantdesigning.com",
        "author"        => "DarSider",
        "authorsite"    => "http://www.elegantdesigning.com",
        "version"       => "1.0",
        "guid"          => "",
        "codename"      => "autoawardgranting",
        "compatibility" => "*"
    );
}

function awardgranting_install()
{
    awardgranting_add_table();
}

function awardgranting_uninstall()
{
    awardgranting_remove_table();
}

function awardgranting_is_installed()
{
    global $db;
    if($db->table_exists('awardgranting')){
        return true;
    } 
    else
    {
        return false;
    }
}

function awardgranting_activate()
{

}

function awardgranting_desactivate()
{

}

include('awardgranting/functions.php');