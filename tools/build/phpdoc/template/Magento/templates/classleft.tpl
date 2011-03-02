{foreach key=subpackage item=files from=$classleftindex}
  <div class="package">
	<ul>
	{if $subpackage != ""}<li class="heading">{$subpackage}</li>{/if}
	{section name=files loop=$files}
		<li>{if $files[files].link != ''}<a href="{$files[files].link}">{/if}{$files[files].title}{if $files[files].link != ''}</a>{/if}</li>
	{/section}
	</ul>
  </div>
{/foreach}
