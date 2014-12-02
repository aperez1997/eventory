
<div ng-app="browsePerformersApp" ng-controller="browsePerformersCtr">
<ul class="nav">
	<li>Sort By:</li>
	<li ng-click="predicate = 'sort_default'; reverse=!reverse">Default</li>
	<li ng-click="predicate = 'name'; reverse=!reverse">Name</li>
	<li ng-click="predicate = 'event_count'; reverse=!reverse">Events</li>
	<li ng-click="predicate = 'updated'; reverse=!reverse">Updated</li>
</ul>
<div ng-show="performerList">
	<ul>
	<li ng-repeat="performer in performerList | orderBy:predicate:reverse">
		<a href="{performer.href}">{performer.name}</a> [{performer.event_count} events] Updated: {performer.updated | date 'medium'}
	</li>
	</ul>
</div>
</div>