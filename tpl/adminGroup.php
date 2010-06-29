<?php
/**
 * adminGroup.php
 * 
 * Shows the group management page at the admin panel.
 * 
 * PHP versions 5
 * 
 * @category  UserAccessManager
 * @package   UserAccessManager
 * @author    Alexander Schneider <alexanderschneider85@googlemail.com>
 * @copyright 2008-2010 Alexander Schneider
 * @license   http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @version   SVN: $Id$
 * @link      http://wordpress.org/extend/plugins/user-access-manager/
 */

/**
 * Inserts or update a user group.
 * 
 * @param integer $userGroupId The id of the user group.
 * 
 * @return null
 */
function insertUpdateGroup($userGroupId)
{
    global $userAccessManager;
    
    if ($userGroupId != null) {
        $uamUserGroup 
            = &$userAccessManager->getAccessHandler()->getUserGroups($userGroupId);
    } else {
        $uamUserGroup 
            = new UamUserGroup($userAccessManager->getAccessHandler(), null);
    }

    $uamUserGroup->setGroupName($_POST['userGroupName']);
	$uamUserGroup->setGroupDesc($_POST['userGroupDescription']);
	$uamUserGroup->setReadAccess($_POST['readAccess']);
	$uamUserGroup->setWriteAccess($_POST['writeAccess']);
	$uamUserGroup->setIpRange($_POST['ipRange']);
        
    if (isset($_POST['roles'])) {
        $roles = $_POST['roles'];
    } else {
        $roles = null;
    }

    $uamUserGroup->unsetRoles(true);
    
    if ($roles) {
        foreach ($roles as $role) {
            $uamUserGroup->addRole($role);
        }
    }
    
    $uamUserGroup->save();
    
    $userAccessManager->getAccessHandler()->addUserGroup($uamUserGroup);
}

/**
 * Prints the group formular.
 * 
 * @param integer $groupId The given group id.
 * 
 * @return null
 */
function getPrintEditGroup($groupId = null)
{
    global $userAccessManager;
    $uamUserGroup = &$userAccessManager->getAccessHandler()->getUserGroups($groupId);
    ?>
	<form method="post" action="<?php 
	    echo reset(
	        explode("?", $_SERVER["REQUEST_URI"])
	    ) . "?page=" . $_GET['page']; 
	?>">
	<?php
    if (isset($groupId)) {
        ?> 
    	<input type="hidden" value="updateGroup" name="action" /> 
    	<input type="hidden" value="<?php echo $groupId; ?>" name="userGroupId" />
		<?php
    } else {
        ?> 
    	<input type="hidden" value="addGroup" name="action" /> 
        <?php
    }
    ?>
    	<table class="form-table">
    		<tbody>
    			<tr class="form-field form-required">
    				<th valign="top" scope="row"><?php echo TXT_GROUP_NAME; ?></th>
    				<td>
    					<input type="text" size="40" value="<?php
    if (isset($groupId)) {
        echo $uamUserGroup->getGroupName();
    } 
                        ?>" id="userGroupName" name="userGroupName" /><br />
		                <?php echo TXT_GROUP_NAME_DESC; ?>
		        	</td>
				</tr>
            	<tr class="form-field form-required">
            		<th valign="top" scope="row"><?php echo TXT_GROUP_DESC; ?></th>
            		<td>
            			<input type="text" size="40" value="<?php 
    if (isset($groupId)) { 
        echo $uamUserGroup->getGroupDesc(); 
    } 
                        ?>" id="userGroupDescription" name="userGroupDescription" /><br />
            		    <?php echo TXT_GROUP_DESC_DESC; ?>
            		</td>
            	</tr>
				<tr class="form-field form-required">
                	<th valign="top" scope="row"><?php echo TXT_GROUP_IP_RANGE; ?></th>
                	<td><input type="text" size="40" value="<?php
    if (isset($groupId)) {
        echo $uamUserGroup->getIpRange('string');
    } 
                        ?>" id="ipRange" name="ipRange" /><br />
                		<?php echo TXT_GROUP_IP_RANGE_DESC; ?>
                	</td>
                </tr>
                <tr class="form-field form-required">
                	<th valign="top" scope="row"><?php echo TXT_GROUP_READ_ACCESS; ?></th>
                	<td>
                		<select name="readAccess">
                			<option value="group"
	<?php
    if (isset($groupId)) {
        if ($uamUserGroup->getReadAccess() == "group") {
            echo 'selected="selected"';
        }
    } 
    ?>
    						>
    						    <?php echo TXT_ONLY_GROUP_USERS ?>
    						</option>
							<option value="all"
	<?php
    if (isset($groupId)) {
        if ($uamUserGroup->getReadAccess() == "all") {
            echo 'selected="selected"';
        }
    } 
    ?>
    						>
    						    <?php echo TXT_ALL ?>
    						</option>
						</select><br />
	                    <?php echo TXT_GROUP_READ_ACCESS_DESC; ?>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th valign="top" scope="row"><?php echo TXT_GROUP_WRITE_ACCESS; ?></th>
					<td>
						<select name="writeAccess">
							<option value="group"
	<?php
    if (isset($groupId)) {
        if ($uamUserGroup->getWriteAccess() == "group") {
            echo 'selected="selected"';
        }
    } 
    ?>
    						>
    					        <?php echo TXT_ONLY_GROUP_USERS ?>
        					</option>
    						<option value="all" 
	<?php 
    if (isset($groupId)) {
        if ($uamUserGroup->getWriteAccess() == "all") {
            echo 'selected="selected"';
        }
    } 
    ?>
    					>
        					    <?php echo TXT_ALL ?>
        					</option>
						</select><br />
	                    <?php echo TXT_GROUP_WRITE_ACCESS_DESC; ?>
	            	</td>
				</tr>
				<tr>
					<th valign="top" scope="row"><?php echo TXT_GROUP_ROLE; ?></th>
					<td>
						<ul class='uam_role'>
	<?php
    global $wp_roles;
    
    if (isset($groupId)) {
        $groupRoles = $uamUserGroup->getRoles();
    }
    
    foreach ($wp_roles->role_names as $role => $name) {
        if ($role != "administrator") {
            ?>
							<li class="selectit">
								<input id="role-<?php echo $role; ?>" type="checkbox"
			<?php
			
            if (isset($groupRoles[$role])) {
                echo 'checked="checked"';
            } 
            ?>
			
								value="<?php echo $role; ?> " name="roles[]" /> 
                				<label for="role-<?php echo $role; ?>">
                			        <?php echo $role; ?>
                				</label>
							</li>
		<?php
        }
    }
    ?>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" value="<?php
    if (isset($groupId)) {
        echo TXT_UPDATE_GROUP;
    } else {
        echo TXT_ADD_GROUP;
    } 
            ?>" name="submit" class="button" />
		</p>
	</form>
    <?php
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = null;
}

if ($action == 'editGroup' && isset($_GET['id'])) {
    $editGroup = true;
} else {
    $editGroup = false;
}

if (isset($_POST['action'])) {
    $postAction = $_POST['action'];
} else {
    $postAction = null;
}

if ($postAction == 'delgroup') {
    if (isset($_POST['delete'])) {
        $delIds = $_POST['delete'];
    }
    if (isset($delIds)) {
        global $userAccessManager;
        
        foreach ($delIds as $delId) {
            $userAccessManager->getAccessHandler()->deleteUserGroup($delId);
        }
        ?>
        <div class="updated">
        	<p><strong><?php echo TXT_DEL_GROUP; ?></strong></p>
        </div>
        <?php
    }
}

if (($postAction == 'updateGroup' || $postAction == 'addGroup') 
    && !empty($_POST['userGroupName'])
) {
    if (!isset($_POST['userGroupId'])) {
        $_POST['userGroupId'] = null;
    }
    
    insertUpdateGroup($_POST['userGroupId']);
    
    if ($postAction == 'addGroup') {
        ?>
        <div class="updated">
        	<p><strong><?php echo TXT_GROUP_ADDED; ?></strong></p>
        </div>
        <?php
    } elseif ($postAction == 'updateGroup') {
        ?>
        <div class="updated">
        	<p><strong><?php echo TXT_ACCESS_GROUP_EDIT_SUC; ?></strong></p>
        </div>
        <?php
    }
} elseif (($postAction == 'updateGroup' || $postAction == 'addGroup') 
         && empty($_POST['userGroupName'])) {
    ?>
    <div class="error">
    	<p><strong><?php echo TXT_GROUP_ERROR; ?></strong></p>
    </div>
    <?php
}

if (!$editGroup) {
    ?>
    <div class=wrap>
        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        	<input type="hidden" value="delgroup" name="action" />
            <h2><?php echo TXT_MANAGE_GROUP; ?></h2>
            <div class="tablenav">
                <div class="alignleft">
                	<input type="submit" class="button-secondary delete" name="deleteit" value="<?php echo TXT_DELETE; ?>" /> 
                	<input type="hidden" id="TXT_COLLAPS_ALL" name="deleteit" value="<?php echo TXT_COLLAPS_ALL; ?>" /> 
                	<input type="hidden" id="TXT_EXPAND_ALL" name="deleteit" value="<?php echo TXT_EXPAND_ALL; ?>" />
                </div>
            	<br class="clear" />
            </div>
            <br class="clear" />
            <table class="widefat">
            	<thead>
            		<tr class="thead">
            			<th scope="col"></th>
            			<th scope="col"><?php echo TXT_NAME; ?></th>
            			<th scope="col"><?php echo TXT_DESCRIPTION; ?></th>
            			<th scope="col"><?php echo TXT_READ_ACCESS; ?></th>
            			<th scope="col"><?php echo TXT_WRITE_ACCESS; ?></th>
            			<th scope="col"><?php echo TXT_IP_RANGE; ?></th>
            			<th scope="col"><?php echo TXT_POSTS; ?></th>
            			<th scope="col"><?php echo TXT_PAGES; ?></th>
            			<th scope="col"><?php echo TXT_FILES; ?></th>
            			<th scope="col"><?php echo TXT_CATEGORIES; ?></th>
            			<th scope="col"><?php echo TXT_USERS; ?></th>
            		</tr>
            	</thead>
        	<tbody>
    <?php
    if (isset($_GET['page'])) {
        $curAdminPage = $_GET['page'];
    }
    
    global $userAccessManager;
    $uamUserGroups = &$userAccessManager->getAccessHandler()->getUserGroups();
    
    if (isset($uamUserGroups)) {
        foreach ($uamUserGroups as $uamUserGroup) {
            ?>
        		<tr class="alternate" id="group-<?php echo $uamUserGroup->getId(); ?>">
        			<th class="check-column" scope="row">
        				<input type="checkbox" value="<?php echo $uamUserGroup->getId(); ?>" name="delete[]" />
        			</th>
        			<td>
        				<strong>
        					<a href="?page=<?php echo $curAdminPage; ?>&action=editGroup&id=<?php echo $uamUserGroup->getId(); ?>">
        					    <?php echo $uamUserGroup->getGroupName(); ?>
        					</a>
        				</strong>
        			</td>
        			<td><?php echo $uamUserGroup->getGroupDesc() ?></td>
        			<td>
            <?php 
            if ($uamUserGroup->getReadAccess() == "all") {
                echo TXT_ALL;
            } elseif ($uamUserGroup->getReadAccess() == "group") {
                echo TXT_ONLY_GROUP_USERS;
            } 
            ?>
                    </td>
        			<td>
    		<?php
            if ($uamUserGroup->getWriteAccess() == "all") {
                echo TXT_ALL;
            } elseif ($uamUserGroup->getWriteAccess() == "group") {
                echo TXT_ONLY_GROUP_USERS;
            } 
            ?>
                	</td>
        			<td>
    		<?php
            if ($uamUserGroup->getIpRange()) {
                ?>
                		<ul>
                <?php
                foreach ($uamUserGroup->getIpRange() as $ipRange) {
                    ?>
                			<li>
                    <?php
                    echo $ipRange;
                    ?>
                			</li>
                    <?php
                }
                ?>
                		</ul>
                <?php
            } else {
                echo TXT_NONE;
            }
            ?>
                	</td>
        			<td>
    		<?php
            if (count($uamUserGroup->getPosts()) > 0) {
                echo count($uamUserGroup->getPosts()) . " " . TXT_POSTS;
            } else {
                echo TXT_NONE;
            }
            ?>
        			</td>
        			<td>
    		<?php
            if (count($uamUserGroup->getPages()) > 0) {
                echo count($uamUserGroup->getPages()) . " " . TXT_PAGES;
            } else {
                echo TXT_NONE;
            }
            ?>
                	</td>
        			<td>
    		<?php
            if (count($uamUserGroup->getFiles()) > 0) {
                echo count($uamUserGroup->getFiles()) . " " . TXT_FILES;
            } else {
                echo TXT_NONE;
            }
            ?>
                    	</td>
            			<td>
    		<?php
            if (count($uamUserGroup->getCategories()) > 0) {
                echo count($uamUserGroup->getCategories()) . " " . TXT_CATEGORIES;
            } else {
                echo TXT_NONE;
            }
            ?>
        			</td>
        			<td>
    		<?php
            if (count($uamUserGroup->getUsers()) > 0) {
                echo count($uamUserGroup->getUsers()) . " " . TXT_USERS;
            } else {
                echo TXT_NONE;
            }
            ?>
                    	</td>
            		</tr>
    		<?php
        }
    }
    ?>
        	</tbody>
        </table>
    </form>
	</div>
    <?php 
}
?>
<div class="wrap">
    <h2>
<?php


if ($editGroup) {
    echo TXT_EDIT_GROUP;
} else {
    echo TXT_ADD_GROUP;
}
?>
	</h2>
<?php 
if ($editGroup) {
    $groupId = $_GET['id'];    
    getPrintEditGroup($groupId);
} else {
    getPrintEditGroup();
}
?>
</div>