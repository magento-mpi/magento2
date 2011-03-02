<?xml version="1.0" encoding="iso-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>#MAGE_TITLE#</title>
	<link rel="stylesheet" type="text/css" href="{$subdir}media/style.css">
	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'/>
</head>
<body>

<div class="header">
    <div class="logo">
        <img src="{$subdir}media/logo.gif" height="47" width="171" alt="" /><img class="slogan" src="{$subdir}media/slogan.gif" height="20" width="242" alt="" />
    </div>
    <h2>#MAGE_TITLE#</h2>
    <h3>version #MAGE_VERSION#</h3>
    <a href="#MAGE_LINK_HREF#" title="#MAGE_LINK_TITLE#">#MAGE_LINK_TEXT#</a>
</div>
<div class="header-menu">
      {assign var="packagehaselements" value=false}
      {foreach from=$packageindex item=thispackage}
        {if in_array($package, $thispackage)}
          {assign var="packagehaselements" value=true}
        {/if}
      {/foreach}
      {if $packagehaselements}
                [ <a href="{$subdir}classtrees_{$package}.html" class="menu">class tree: {$package}</a> ]
                [ <a href="{$subdir}elementindex_{$package}.html" class="menu">index: {$package}</a> ]
      {/if}
      [ <a href="{$subdir}elementindex.html" class="menu">all elements</a> ]
</div>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr valign="top">
    <td width="195" class="menu">
		<div class="package-title">{$package}</div>
{if count($ric) >= 1}
  <div class="package">
	<div id="ric">
		{section name=ric loop=$ric}
			<p><a href="{$subdir}{$ric[ric].file}">{$ric[ric].name}</a></p>
		{/section}
	</div>
	</div>
{/if}
{if $hastodos}
  <div class="package">
	<div id="todolist">
			<p><a href="{$subdir}{$todolink}">Todo List</a></p>
	</div>
	</div>
{/if}
      <h4>Packages:</h4>
  <div class="package">
      <ul>
      {section name=packagelist loop=$packageindex}
        <li><a href="{$subdir}{$packageindex[packagelist].link}">{$packageindex[packagelist].title}</a></li>
      {/section}
      </ul>
  </div>
{if $tutorials}
		<b>Tutorials/Manuals:</b><br />
  <div class="package">
		{if $tutorials.pkg}
			<strong>Package-level:</strong>
			{section name=ext loop=$tutorials.pkg}
				{$tutorials.pkg[ext]}
			{/section}
		{/if}
		{if $tutorials.cls}
			<strong>Class-level:</strong>
			{section name=ext loop=$tutorials.cls}
				{$tutorials.cls[ext]}
			{/section}
		{/if}
		{if $tutorials.proc}
			<strong>Procedural-level:</strong>
			{section name=ext loop=$tutorials.proc}
				{$tutorials.proc[ext]}
			{/section}
	</div>
		{/if}
{/if}
      {if !$noleftindex}{assign var="noleftindex" value=false}{/if}
      {if !$noleftindex}
      {if $compiledfileindex}
      <h4>Files:</h4>
      {eval var=$compiledfileindex}
      {/if}
      {if $compiledinterfaceindex}
      <h4>Interfaces:</h4>
      {eval var=$compiledinterfaceindex}
      {/if}
      {if $compiledclassindex}
      <h4>Classes:</h4>
      {eval var=$compiledclassindex}
      {/if}
      {/if}
    </td>
    <td>
      <table cellpadding="10" cellspacing="0" width="100%" border="0"><tr><td valign="top">

{if !$hasel}{assign var="hasel" value=false}{/if}
{if $eltype == 'class' && $is_interface}{assign var="eltype" value="interface"}{/if}
{if $hasel}
<h1>{$eltype|capitalize}: {$class_name}</h1>
Source Location: {$source_location}<br /><br />
{/if}
