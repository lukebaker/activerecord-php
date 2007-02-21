<?php
require_once 'Post.php';
require_once 'Comment.php';

$p = new Post;
$p->title = "The best post ever";
$p->id = 2;
#var_dump($p);

$p->body = "This is my best blog entry ever.";
#var_dump($p->body);

#var_dump(Post::find());
#var_dump($p->comments);

#var_dump(new Post(array('title' => 'booyah!!')));
$c = new Comment( array('post_id' => 1));
$c->post = $p;
#var_dump($c->post_id);
#var_dump($c->post);
$a = new ActiveRecordCollection(array(1,2,3));
var_dump($a);
var_dump(is_array($a));

?>
