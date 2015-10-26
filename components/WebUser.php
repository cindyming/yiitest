<?php
class WebUser extends CWebUser
{
	public function hasPermission($permissions)
	{
		$userPermissions = $this->getState('permissions');

		if(is_array($permissions))
		{
			foreach($permissions as $permission)
			{
				if(in_array($permission,$userPermissions))
				{
					return true;
				}
			}
		}
		else {
			return in_array($permissions,$userPermissions);
		}

		return false;
	}

	public function isAdmin()
	{
		return 1==$this->getState('role_id');
	}
}