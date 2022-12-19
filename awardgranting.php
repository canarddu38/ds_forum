<?php

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

switch($mybb->input['action'])
{
	case 'index':
		$page->add_breadcrumb_item("Index");
		break;
	case 'add':
		$page->add_breadcrumb_item("Add");
		break;
}

if($mybb->input['action'] == "index" or empty($mybb->input['action']))
{
	
	$page->output_header("Index Page");
	$sub_tabs['awardgranting_home'] = array(
		'title' => "Index Panel",
		'link' => "index.php?module=tools-awardgranting",
	);
	$sub_tabs['awardgranting_add'] = array(
		'title' => "Add",
		'link' => "index.php?module=tools-awardgranting&amp;action=add",
	);
	$page->output_nav_tabs($sub_tabs, 'awardgranting_home');
    
	$table = new Table;
	$table->construct_header('Award ID', array('class' => 'align_center'), array('width' => '20%'));
	$table->construct_header('Achievement Type', array('class' => 'align_center', 'width' => '30%'));
	$table->construct_header('Value', array('class' => 'align_center', 'width' => '20%'));
    $table->construct_header('Controls', array('class' => 'align_center', 'width' => '30%'));
    
    $query = $db->query("SELECT * FROM ".TABLE_PREFIX."awardgranting");
    while($task = $db->fetch_array($query))
    {
        if ($task['actype'] == "posts") 
        {
            $task['actype'] = "Posts Count";
        }
        if ($task['actype'] == "onlinetime") 
        {
            $task['actype'] = "Online Time";
        }
        if ($task['actype'] == "reputation") 
        {
            $task['actype'] = "Reputation";
        }
        if ($task['actype'] == "newpoints") 
        {
            $task['actype'] = "Points";
        }
        $table->construct_cell($task['awardid'], array('class' => 'align_center'));
		$table->construct_cell($task['actype'], array('class' => 'align_center'));
		$table->construct_cell($task['acvalue'], array('class' => 'align_center'));
		$table->construct_cell("<a href='index.php?module=tools-awardgranting&amp;action=edit&amp;id={$task['id']}'>Edit</a> - <a href='index.php?module=tools-awardgranting&amp;action=delete&amp;id={$task['id']}'>Delete</a>", array('class' => 'align_center'));
		$table->construct_row();
    }
    
    if($table->num_rows() == 0)
	{
		$table->construct_cell("There are no granting tasks.", array('colspan' => '4'));
		$table->construct_row();
	}
    $table->output("Manage Granting Tasks");
	$page->output_footer();
}

if($mybb->input['action'] == 'add')
{
	
	$page->output_header("Add a granting task");

	if($mybb->request_method == "post")
	{
        $new_task = array(
				"awardid" => $mybb->input['awardid'],
				"actype" => $mybb->input['actype'],
				"acvalue" => $mybb->input['acvalue']
        );
        $did = $db->insert_query("awardgranting", $new_task);
        if($did)
        {
            flash_message("Task Added Succesfully", 'success');
            admin_redirect("index.php?module=tools-awardgranting");
        }
        else
        {
            flash_message("Error. Please check the validity of the entries", 'error');
			admin_redirect("index.php?module=tools-awardgranting&action=add");
		}
	}


	$sub_tabs['awardgranting_home'] = array(
		'title' => "Index Panel",
		'link' => "index.php?module=tools-awardgranting",
	);
	$sub_tabs['awardgranting_add'] = array(
		'title' => "Add",
		'link' => "index.php?module=tools-awardgranting&amp;action=add",
	);

	// SAY WHICH TAB IS VISIBLE
	$page->output_nav_tabs($sub_tabs, 'awardgranting_add');

	// CREATE A FORM
	$form = new Form("index.php?module=tools-awardgranting&action=add", "post", "add");

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$form_container = new FormContainer("Add Granting Task");

	$form_container->output_row("Award ID", "Enter the ID of the award", $form->generate_text_box('awardid', $mybb->input['awardid'], array('id' => 'awardid')), 'awardid');
    
    $form_container->output_row("Achievement Type", "Achievement type when granting an award", $form->generate_select_box("actype", array('posts' => 'Posts Count', 'onlinetime' => 'Online Time', 'reputation' => 'Reputation', 'newpoints' => 'Points'), $selected=array(), $options=array()));
    
    $form_container->output_row("Value", "P/S: write value in numbers, seconds for the online time. Example : 45", $form->generate_text_box('acvalue', $mybb->input['acvalue'], array('id' => 'acvalue')), 'acvalue');
	
	$buttons[] = $form->generate_submit_button("Add");
	$form_container->end();
	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == 'edit')
{
	$page->output_header("Edit a granting task");
    $task_id = intval($mybb->input['id']);
    
	if($mybb->request_method == "post")
	{
        $did = $db->query("UPDATE ".TABLE_PREFIX."awardgranting SET awardid='{$mybb->input['awardid']}', actype='{$mybb->input['actype']}', acvalue='{$mybb->input['acvalue']}' WHERE id='{$task_id}'");
        if($did)
        {
            flash_message("Task Edited Succesfully", 'success');
            admin_redirect("index.php?module=tools-awardgranting");
        }
        else
        {
            flash_message("Error. Please check the validity of the entries", 'error');
			admin_redirect("index.php?module=tools-awardgranting");
		}
	}


	$sub_tabs['awardgranting_home'] = array(
		'title' => "Index Panel",
		'link' => "index.php?module=tools-awardgranting",
	);
	$sub_tabs['awardgranting_add'] = array(
		'title' => "Add",
		'link' => "index.php?module=tools-awardgranting&amp;action=add",
	);
    $sub_tabs['awardgranting_edit'] = array(
		'title' => "Edit",
		'link' => "index.php?module=tools-awardgranting&amp;action=edit",
	);

	$page->output_nav_tabs($sub_tabs, 'awardgranting_edit');

	// CREATE A FORM
	$form = new Form("index.php?module=tools-awardgranting&action=edit&id={$mybb->input['id']}", "post", "edit");

	if($errors)
	{
		$page->output_inline_error($errors);
	}
    $form_container = new FormContainer("Editing Granting Task");

    $form_container->output_row("Award ID", "Enter the ID of the award", $form->generate_text_box('awardid', $mybb->input['awardid'], array('id' => 'awardid')), 'awardid');
    $form_container->output_row("Achievement Type", "Achievement type when granting an award", $form->generate_select_box("actype", array('posts' => 'Posts Count', 'onlinetime' => 'Online Time', 'reputation' => 'Reputation', 'newpoints' => 'Points'), $selected=array(), $options=array()));
    $form_container->output_row("Value", "P/S: write value in numbers, seconds for the online time. Example : 45", $form->generate_text_box('acvalue', $mybb->input['acvalue'], array('id' => 'acvalue')), 'acvalue');
    $buttons[] = $form->generate_submit_button("Add");
    $form_container->end();
    $form->output_submit_wrapper($buttons);
    $form->end();
	$page->output_footer();
}

if($mybb->input['action'] == 'delete')
{
    $task_id = intval($mybb->input['id']);
    $did = $db->query("DELETE FROM ".TABLE_PREFIX."awardgranting WHERE id = {$task_id}");
    if($did)
    {
        flash_message("Task Removed Succesfully", 'success');
        admin_redirect("index.php?module=tools-awardgranting");
    }
    else
    {
       flash_message("Error. Please check the validity of the entries", 'error');
	   admin_redirect("index.php?module=tools-awardgranting");
    }
}
?>