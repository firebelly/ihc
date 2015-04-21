<section class="thought active">
<div class="thought-wrapper">
	<h2>Thought of the Day</h2>
	<?= \Firebelly\PostTypes\Thought\get_thought_of_the_day(!empty($focus_area) ? $focus_area : ''); ?>
	<a class="submit-thought" href="#">Submit A Thought</a>
</div>

<div class="submit-thought-wrapper hide">
	<h2>Submit Your Thought</h2>
	<?= \Firebelly\PostTypes\Thought\submit_form(); ?>
</div>
</section>