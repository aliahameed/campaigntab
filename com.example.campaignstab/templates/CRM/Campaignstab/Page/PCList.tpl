{if $rows}
<div id="ltype">
<p></p>
{include file="CRM/common/pager.tpl" location="top"}
{include file="CRM/common/jsortable.tpl"}
{strip}
<table id="options" class="display">
  <thead>
    <tr>
    <th>{ts}Page Title{/ts}</th>
    <th>{ts}Supporter{/ts}</th>
    <th>{ts}Contribution Page / Event{/ts}</th>
    <th>{ts}Number of Contributions{/ts}</th>
    <th>{ts}Amount Raised{/ts}</th>
    <th>{ts}Goal Amount{/ts}</th>
    <th>{ts}Status{/ts}</th>
    <th></th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$rows item=row}
  <tr id="row_{$row.id}" class="{$row.class}">
    <td><a href="{crmURL p='civicrm/pcp/info' q="reset=1&id=`$row.id`" fe='true'}" title="{ts}View Personal Campaign Page{/ts}" target="_blank">{$row.title}</a></td>
    <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.supporter_id`"}" title="{ts}View contact record{/ts}">{$row.supporter}</a></td>
    <td><a href="{$row.page_url}" title="{ts}View page{/ts}" target="_blank">{$row.page_title}</td>
    <td>{$row.count}</td>
    <td>{$row.amount_raised}</td>
    <td>{$row.goal_amount}</td>
    <td>{$row.status_id}</td>
    <td id={$row.id}>{$row.action|replace:'xx':$row.id}</td>
  </tr>
  {/foreach}
  </tbody>
</table>
{/strip}
</div>
{else}
<div class="messages status no-popup">
<div class="icon inform-icon"></div>
    {if $isSearch}
        {ts}There are no Personal Campaign Pages which match your search criteria.{/ts}
    {else}
        {ts}There are currently no Personal Campaign Pages for this contact.{/ts}
    {/if}
</div>
{/if}