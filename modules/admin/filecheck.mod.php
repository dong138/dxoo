<?php

/**
 * 模块：文件校验
 * @package module
 * @name filecheck.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	var $md5data = array();
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function main()
	{
		$this->CheckAdminPrivs('filecheck');
		$step = 1;
		include handler('template')->file('@admin/file_check');
	}

	public function check()
	{
		$this->CheckAdminPrivs('filecheck');
		if (!$tttgfiles = @file('./tttgfiles.md5'))
		{
			$this->Messager("操作失败，没有找到密钥文件",'?mod=filecheck');
		}
		$step = 2;
		include handler('template')->file('@admin/file_check');
	}

	public function checkout()
	{
		$this->CheckAdminPrivs('filecheck');
		$step = 3;
		$this->checkfiles('./', '\.php', 0, 'md5.php');
		$this->checkfiles('api/', '\.php');
		$this->checkfiles('data/install/', '\.sql');
		$this->checkfiles('app/', '\.php', 1);
		$this->checkfiles('modules/', '\.php', 1);
		$this->checkfiles('include/', '\.php', 1, 'api,api_gbk');
		$this->checkfiles('static/', '\.js|\.css', 1, 'addon,images');
		$this->checkfiles('templates/', '\.php|\.html|\.js|\.css', 1, 'images,shihuidaodi_back');

		$tttgfiles = @file('./tttgfiles.md5');
		$md5datanew = $modifylist = array();
		foreach ($tttgfiles as $line)
		{
			$file = trim(substr($line, 34));
			$md5datanew[$file] = substr($line, 0, 32);
			if ($md5datanew[$file] != $this->md5data[$file])
			{
				$modifylist[$file] = $this->md5data[$file];
			}
			$md5datanew[$file] = $this->md5data[$file];
		}
		$weekbefore = time() - 604800;  		$addlist = @array_diff_assoc($this->md5data, $md5datanew);
		$dellist = @array_diff_assoc($md5datanew, $this->md5data);
		$modifylist = @array_diff_assoc($modifylist, $dellist);
		$showlist = @array_merge($this->md5data, $md5datanew);

		$result = $dirlog = array();

		foreach ($showlist as $file => $md5)
		{
			$dir = dirname($file);
			$statusf = $statust = 1;
			if (@array_key_exists($file, $modifylist))
			{
				$status = '<em class="edited">被修改</em>';
				if (!isset($dirlog[$dir]['modify']))
				{
					$dirlog[$dir]['modify'] = '';
				}
				$dirlog[$dir]['modify']++;  				$dirlog[$dir]['marker'] = substr(md5($dir),0,4);
			}
			elseif (@array_key_exists($file, $dellist))
			{
				$status = '<em class="del">被删除</em>';
				if (!isset($dirlog[$dir]['del']))
				{
					$dirlog[$dir]['del'] = '';
				}
				$dirlog[$dir]['del']++;     				$dirlog[$dir]['marker'] = substr(md5($dir),0,4);
			}
			elseif (@array_key_exists($file, $addlist))
			{
				$status = '<em class="unknown">未知</em>';
				if (!isset($dirlog[$dir]['add']))
				{
					$dirlog[$dir]['add'] = '';
				}
				$dirlog[$dir]['add']++;     				$dirlog[$dir]['marker'] = substr(md5($dir),0,4);
			}
			else
			{
				$status = '<em class="correct">正确</em>';
				$statusf = 0;
			}

						$filemtime = @filemtime(ROOT_PATH.$file);
			if ($filemtime > $weekbefore)
			{
				$filemtime = '<b>'.date("Y-m-d H:i:s", $filemtime).'</b>';
			}
			else
			{
				$filemtime = date("Y-m-d H:i:s", $filemtime);
				$statust = 0;
			}

			if ($statusf)
			{
				$filelist[$dir][] = array('file' => basename($file), 'size' => file_exists(ROOT_PATH.$file) ? number_format(filesize(ROOT_PATH.$file)).' Bytes' : '', 'filemtime' => $filemtime, 'status' => $status);
			}
		}

		$result['<em class="edited">被修改</em>'] = count($modifylist);
        $result['<em class="del">被删除</em>'] = count($dellist);
        $result['<em class="unknown">未知</em>'] = count($addlist);

		include handler('template')->file('@admin/file_check');
	}

	
	private function checkfiles($incurrentdir, $ext = '', $sub = 1, $skip = '')
	{
		$currentdir = ROOT_PATH.str_replace(ROOT_PATH, '', $incurrentdir);
		$dir = @opendir($currentdir);
		$exts = '/('.$ext.')$/i';
		$skips = explode(',', $skip);
		while ($entry = @readdir($dir))
		{
			$file = $currentdir.$entry;
			$infile = $incurrentdir.$entry;
			if ($entry != '.' && $entry != '..' && $entry != '.svn' && (preg_match($exts, $entry) || ($sub && is_dir($file))) && !in_array($entry, $skips))
			{
				if ($sub && is_dir($file))
				{
					$this->checkfiles($infile.'/', $ext, $sub, $skip);
				}
				else
				{
					$this->md5data[$infile] = md5_file($file);
				}
			}
		}
	}
}
?>