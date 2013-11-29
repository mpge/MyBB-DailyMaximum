<?php
/**
 * DailyMaximum
 * Copyright 2013 Ziloi.com (Ziloi) on PluginBIN.com (License: MIT)
 * Developed for Ziloi.com (Ziloi) on PluginBIN.com by Matthew Gross (http://mattgross.net)
 * http://www.pluginbin.com/author/ziloi
 */
 
    if (!defined("IN_MYBB"))
    {
        die("You CANNOT Access this File Directly!");
    }
 
    // Hooks
    
    // Continue:
 
    function dailymaximum_info()
    {
        return array(
            "name"           => "Daily Maximum",
            "description"    => "A MyBB Plugin to limit the number of posts a user post in a specific forum per day.",
            "website"        => "http://pluginbin.com/author/ziloidev",
            "author"         => "Ziloi Plugin Development",
            "authorsite"     => "http://www.pluginbin.com/author/ziloi",
            "version"        => "1.0BETA",
            "guid"           => "",
            "compatibility"  => "*",
        );
    }
    
    function dailymaximum_activate()
    {
        global $db;
        
        $dailymaximum_group = array(
            'gid'           => 'NULL',
            'name'          => 'dailymaximum',
            'title'         => 'DailyMaximum',
            'description'   => "A MyBB Plugin to limit the number of posts a user post in a specific forum per day.";
            'disporder'     => "13",
            'isdefault'     => "no",
        );
    
        $db->insert_query('settinggroups', $dailymaximum_group);
        $gid = $db->insert_id();
        
        $dailymaximum_setting_1 = array(
            "sid"           => "NULL",
            "name"          => "dailymaximum_1",
            "title"         => "Enable/Disable Daily Maximum",
            "description"   => "Toggles the Plugin ON or OFF",
            "optionscode"   => "onoff",
            "value"         => '1',
            "disporder"     => 1,
            "gid" => intval($gid),
        );
        
        $dailymaximum_setting_2 = array(
            "sid"           => "NULL",
            "name"          => "dailymaximum_2",
            "title"         => "Toggle Feature For Specific Forums",
            "description"   => "Which Forums (ID) would you like this feature to be running? 
            One per line with equals sign and then usergroup ids seperated by commas 
            followed by a colon and the amount of posts per set time followed by a 
            dash and the amount of days (24 hours) that the amount of posts will last for. 
            (Example: 1=2,4:10-2 Would mean the users in the usergroup 2 and 4 would only be
            able to post in the forum id 1, 10 times every 2 days). You can generate this at 
            http://ziloi.com/tools/dailymaximum/generate-config.php",
            "optionscode"   => "textarea",
            "value"         => "",
            "disporder"     => 2,
            "gid" => intval($gid),
        );
        
        $dailymaximum_setting_3 = array(
            "sid"           => "NULL",
            "name"          => "dailymaximum_3",
            "title"         => "URL to Redirect to for user once they hit their daily post maximum (Optional - Leave blank for default)".
            "description"   => "",
            "optionscode"   => "text",
            "value"         => "",
            "disporder"     => 3,
            "gid"           => intval($gid),
        );
        
        // Insert Queries
        $db->insert_query("settings", $dailymaximum_setting_1);
        $db->insert_query("settings", $dailymaximum_setting_2);
        $db->insert_query("settings", $dailymaximum_setting_3);
        // Rebuild
        rebuild_settings();
    }
    
    function dailymaximum_deactivate()
    {
        global $db;
        $db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='dailymaximum'");
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='dailymaximum_1'");
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='dailymaximum_2'");
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='dailymaximum_3'");
    }
    
    function dailymaximum_start()
    {
        global $dailymaximum,$mybb;
        if ($mybb->settings['dailymaximum_1'] == 1)
        {
            // Bind settings to variables
            $dailymaximum_2 = $mybb->settings['dailymaximum_2'];
            $dailymaximum_3 = $mybb->settings['dailymaximum_3'];
            $settings_array = array();
            if (isset($dailymaximum_2) && !empty($dailymaximum_2))
            {
                $regex = "(.*?)\=(.*?)\:(.*?)\-(.*?)$";
                foreach(explode("\n", $dailymaximum_2) as $line)
                {
                    if($c=preg_match_all ("/".$regex."/is", $text, $matches))
                    {
                        $forumid = $matches[1][0];
                        $usergroups = explode(",", $matches[2][0]);
                        $postamount = $matches[3][0];
                        $daycount = $matches[4][0];
                        $settings_array[$forumid] = array(
                            "usergroups"    => $usergroups,
                            "postamount"    => $postamount,
                            "daycount"      => $daycount,
                        );
                    }
                }
            }
        }
    }
    
?>
