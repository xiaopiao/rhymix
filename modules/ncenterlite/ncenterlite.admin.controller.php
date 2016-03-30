<?php
class ncenterliteAdminController extends ncenterlite
{
	function procNcenterliteAdminInsertConfig()
	{
		$oModuleController = getController('module');
		$obj = Context::getRequestVars();
		$config = getModel('ncenterlite')->getConfig();

		$config_vars = array(
			'use',
			'display_use',
			'user_config_list',
			'mention_format',
			'mention_names',
			'document_notify',
			'hide_module_srls',
			'mention_format',
			'admin_comment_module_srls',
			'skin',
			'mskin',
			'mcolorset',
			'colorset',
			'zindex',
			'anonymous_name',
			'document_read',
			'layout_srl',
			'mlayout_srl',
			'document_notify'
		);
		if(!$obj->use && $obj->disp_act == 'dispNcenterliteAdminConfig')
		{
			$config->use = array();
		}
		foreach($config_vars as $val)
		{
			if($obj->{$val})
			{
				$config->{$val} = $obj->{$val};
			}
			if($obj->disp_act == 'dispNcenterliteAdminConfig' && !$obj->anonymous_name)
			{
				$config->anonymous_name = null;
			}
			if($obj->disp_act == 'dispNcenterliteAdminConfig' && !$obj->mention_format)
			{
				$config->mention_format = array();
			}
			if($obj->disp_act == 'dispNcenterliteAdminSeletedmid' && !$obj->hide_module_srls)
			{
				$config->hide_module_srls = array();
			}
			if($obj->disp_act == 'dispNcenterliteAdminSeletedmid' && !$obj->admin_comment_module_srls)
			{
				$config->admin_comment_module_srls = array();
			}
		}
		$output = $oModuleController->updateModuleConfig('ncenterlite', $config);
		if(!$output->toBool())
		{
			return new Object(-1, 'ncenterlite_msg_setting_error');
		}

		$this->setMessage('success_updated');

		if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON')))
		{
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', $obj->disp_act);
			header('location: ' . $returnUrl);
			return;
		}
	}

	/**
	 * @brief 스킨 테스트를 위한 더미 데이터 생성 5개 생성
	 **/
	function procNcenterliteAdminInsertDummyData()
	{
		$oNcenterliteController = getController('ncenterlite');
		$logged_info = Context::get('logged_info');

		for($i = 1; $i <= 5; $i++)
		{
			$args = new stdClass();
			$args->member_srl = $logged_info->member_srl;
			$args->srl = 1;
			$args->target_srl = 1;
			$args->type = $this->_TYPE_TEST;
			$args->target_type = $this->_TYPE_TEST;
			$args->target_url = getUrl('');
			$args->target_summary = Context::getLang('ncenterlite_thisistest') . rand();
			$args->target_nick_name = $logged_info->nick_name;
			$args->regdate = date('YmdHis');
			$args->notify = $oNcenterliteController->_getNotifyId($args);
			$output = $oNcenterliteController->_insertNotify($args);
		}
	}

	/**
	 * @brief 모듈 푸시 테스트를 위한 더미 데이터 생성 1개 생성
	 **/
	function procNcenterliteAdminInsertPushData()
	{
		$oNcenterliteController = getController('ncenterlite');
		$logged_info = Context::get('logged_info');

		$args = new stdClass();
		$args->member_srl = $logged_info->member_srl;
		$args->srl = 1;
		$args->target_srl = 1;
		$args->type = $this->_TYPE_DOCUMENT;
		$args->target_type = $this->_TYPE_COMMENT;
		$args->target_url = getUrl('');
		$args->target_summary = Context::getLang('ncenterlite_thisistest') . rand();
		$args->target_nick_name = $logged_info->nick_name;
		$args->regdate = date('YmdHis');
		$args->notify = $oNcenterliteController->_getNotifyId($args);
		$output = $oNcenterliteController->_insertNotify($args);
	}

	function procNcenterliteAdminDeleteNofity()
	{
		$old_date = Context::get('old_date');
		$args = new stdClass;
		if($old_date)
		{
			$args->old_date = $old_date;
		}
		$output = executeQuery('ncenterlite.deleteNotifyAll', $args);
		if(!$output->toBool())
		{
			$oDB->rollback();
			return $output;
		}

		if($old_date)
		{
			$oNcenterliteModel = getModel('ncenterlite');
			$message = Context::getLang('ncenterlite_message_delete_notification_before');
			$message = sprintf($message, $oNcenterliteModel->getAgo($old_date) );
			$this->setMessage($message);
		}
		else
		{
			$this->setMessage('ncenterlite_message_delete_notification_all');
		}
		
		if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON')))
		{
			$returnUrl = Context::get('success_return_url') ?  Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispNcenterliteAdminList');
			header('location: ' .$returnUrl);
			return;
		}
	}
}
