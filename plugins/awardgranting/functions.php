<?php

function awardgranting_menu(&$sub_menu)
{
    global $mybb;
    
    end($sub_menu);
    $key = (key($sub_menu))+10;
        
    if(!$key)
    {
        $key = '160';
    }
        
    $sub_menu[$key] = array('id' => 'awardgranting', 'title' => 'Auto Award Granting', 'link' => "index.php?module=tools-awardgranting");    
}

function awardgranting_action_handler(&$action)
{
    $action['awardgranting'] = array('active' => 'awardgranting', 'file' => 'awardgranting.php');
}

function awardgranting_admin()
{
    global $page, $mybb;
    if($page->active_action != "awardgranting")
    {
        return;
    }
    $page->add_breadcrumb_item("Automatic Award Granting");
} 

function awardgranting_add_table()
{
    global $db, $mybb;
    $db->write_query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."awardgranting` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
  `awardid` int(10) NOT NULL, 
  `actype` varchar(50) NOT NULL,
  `acvalue` int(10) NOT NULL)
  ");
} 

function awardgranting_remove_table()
{
    global $db, $mybb;
    $query = "DROP TABLE `".TABLE_PREFIX."awardgranting`";
    $db->query($query);
}

function awardgranting_dogrant()
{
    global $db, $mybb;
    if ($mybb->user['uid'] != 0 or $mybb->user['usergroup'] != 7 or $mybb->user['usergroup'] != 1)
        $tasks = $db->query("SELECT * FROM ".TABLE_PREFIX."awardgranting");
        while ($task = $db->fetch_array($tasks))
        {
            $already = 0;
            $reason = "";
            $reached = 0;
            $awardid = $task['awardid'];
            $actype = $task['actype'];
            $acvalue = $task['acvalue'];
            switch($actype)
            {
                case 'onlinetime':
                    $acvalue = $acvalue;
                    if ($mybb->user['timeonline'] >= $acvalue)
                    {
                        $acvalue = trim($acvalue);
                        $reason = "Reached $acvalue of online time";
                        $reached = 1;
                    }
                    break;
                case 'posts':
                    if ($mybb->user['postnum'] >= $acvalue)
                    {
                        $reason = "Reached $acvalue posts";
                        $reached = 1;
                    }
                    break;
                case 'reputation':
                    if ($mybb->user['reputation'] >= $acvalue)
                    {
                        $reason = "Reached $acvalue reputations points";
                        $reached = 1;
                    }
                    break;
                case 'newpoints':
                    if ($mybb->user['newpoints'] >= $acvalue)
                    {
                        $reason = "Reached $acvalue points";
                        $reached = 1;
                    }
                    break;
            }
            $own_awards = $db->query("SELECT * FROM ".TABLE_PREFIX."myawards_users WHERE awuid = {$mybb->user['uid']}");
            while ($own_award = $db->fetch_array($own_awards))
            {
                if ($own_award['awid'] == $awardid)
                {
                    $already = $already + 1;
                }
            }
            if ($already == 0 and $reached == 1)
            {
                $time = time();
                $db->query("INSERT INTO ".TABLE_PREFIX."myawards_users(awuid, awid, awreason, awutime) VALUES({$mybb->user['uid']}, {$awardid},  '{$reason}', {$time})");
                $db->query("UPDATE ".TABLE_PREFIX."users SET awards=awards+1 WHERE uid='".$mybb->user['uid']."'");
                require_once(MYBB_ROOT . "/inc/datahandlers/pm.php");

                $pmhandler = new PMDataHandler();

                $pmmessage = "You have got an award for : ".$reason;

                $pm = array(
                    "subject" => "You have been granted an award",
                    "message" => $pmmessage,
                    "icon" => 0,
                    "fromid" => 1,
                    "do" => '',
                    "pmid" => ''
                );

                $pm['toid'] = explode(",", $mybb->user['uid']);
                $pm['toid'] = array_map("trim", $pm['toid']);

                $pm['options'] = array(
                    "savecopy" => 0,
                    "saveasdraft" => 0,
                    "signature" => 0,
                    "disablesmilies" => 0,
                );

                $pmhandler->set_data($pm);

                if($pmhandler->validate_pm())
                {
                    $pminfo = $pmhandler->insert_pm();
                }
            }
            $already = 0;
            $reason = "";
            $reached = 0;
        }
}