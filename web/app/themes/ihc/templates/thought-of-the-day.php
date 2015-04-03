<h2>Thought of the Day</h2>
<div class="thought-wrapper">
	<?= \Firebelly\PostTypes\Thought\get_thought(!empty($focus_area) ? $focus_area : ''); ?>
	<a class="submit-thought" href="#">Submit A Thought</a>
</div>

<div class="submit-thought-wrapper hide">
	<?= \Firebelly\PostTypes\Thought\submit_form(); ?>
</div>