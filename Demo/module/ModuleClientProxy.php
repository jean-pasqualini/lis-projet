<?php
class module_ModuleClientProxy extends ModuleBase implements IModuleBase
{
	public function __call($method,$arguments)
	{
		__call($method,$arguments);
	}
}
?>