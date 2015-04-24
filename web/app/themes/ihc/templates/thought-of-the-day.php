<section class="thought-of-the-day">
<div class="thought-wrapper">
	<h4 class="flag">Thought of the Day</h4>
	<?= \Firebelly\PostTypes\Thought\get_thought_of_the_day(!empty($focus_area) ? $focus_area : ''); ?>
	<a class="submit-thought button" href="#">Submit A Thought</a>
</div>

<div class="submit-thought-wrapper hide">
	<h4 class="flag">Submit Your Thought</h4>
	<?= \Firebelly\PostTypes\Thought\submit_form(); ?>
</div>
</section>