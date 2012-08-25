# ActiveRecord In PHP

## Motiviation

I wrote this after having been spoiled by Ruby on Rails’ implementation
of the ActiveRecord pattern, while still needing to work primarily in
PHP. When I started this, there did exist some
<acronym title="Object-relational mapping">ORM</acronym> options in PHP.
However, I wasn’t satisfied with any one in particular. My goals were to
create an implementation that was very similar to the Rails syntax, easy
to install, and fast.

## Requirements

-   PHP5
-   Naming of tables and columns that follows the Rails convention.


## Installation

1.  Create your database and tables, if you haven’t already. (remember
    use Rails’ conventions for table and column names)
2.  Download [recent ActiveRecord release][] or

        git clone https://github.com/lukebaker/activerecord-php.git

3.  Untar into a models/ directory within your project or move checked
    out directory activerecord-php/ into your models/ directory.
4.  There should now be a models/activerecord-php/ directory, edit
    models/activerecord-php/config.php to your liking.
5.  Run models/activerecord-php/generate.php
6.  This should have have generated model stubs inside your models/
    directory. Edit these model files to tell ActiveRecord about the
    relationships between tables. Do not edit \*Base.php files as they
    get overwritten every time you run generate.php
7.  Use ActiveRecord, by including the models that you want to use:

        require_once 'models/Post.php';

  [recent ActiveRecord release]: https://github.com/lukebaker/activerecord-php/tags

## Example

### Create

```php
$p = new Post(array('title' => 'First Post!11!', 'body' => 'This is the body of my post'));
$p->save(); # saves this post to the table
 
$p2 = new Post();
$p2->title = "Second Post";
$p2->body = "This is the body of the second post";
$p2->save(); # save yet another post to the db
```

### Retrieve

```php
$p = Post::find(1); # finds the post with an id = 1
$p->title; # title of this post
$p->body;  # body of this post
 
# returns the 10 most recent posts in an array, assuming you have a column called "timestamp"
$posts = Post::find('all', array('order' => 'timestamp DESC', 'limit' => 10));
```

### Update

```php
$p = Post::find(1);
$p->title = "Some new title";
$p->save(); # saves the change to the post
 
# alternatively, the following is useful when a form submits an array
$_POST['post'] = array('title' => 'New Title', 'body' => 'New body here!');
$p = Post::find(1);
$p->update_attributes($_POST['post']); # saves the object with these attributes updated
```

### Destroy

```php
$p = Post::find(1);
$p->destroy();
```

### Relationships

```php
$p = Post::find(1);
# call to $p->comments results in query to get all comments for this post
# a subsequent call to $p->comments would not result in a query, but use results from previous query
foreach ($p->comments as $comment) {
  echo $comment->content;
}
```

## Documentation

While this attempts to document most of the features of ActiveRecord, it may not be entirely complete. I've tried to create tests for all pieces of functionality that exist in ActiveRecord. To view and / or run these tests check out the devel/ branch in the Subversion repository. In other words, there may be some functionality that is not documented here but is used in the tests.

For example purposes, let’s pretend we’re building a blog. You’ll have model classes which are each the model of a database table. Each model class is in a separate file. The stubs of these files are automatically generated for you by generate.php. Every time you update your database schema, you'll have to run generate.php again. It will not overwrite the files you've altered, but will overwrite the \*Base.php files. Once you have the model stubs generated you can use them and work with the tables individually. However, in order to use the relationship specific abilities of ActiveRecord, you’ll need to specify the relationships in your models as outlined below in the Associations section.

### Associations

In ActiveRecord we specify relationships between the tables in the model
classes. There are 3 types of relationships, 1:1, 1:many, and many:many.

#### 1:1

In our example, blog posts have a 1:1 relationship with slugs. Here’s
how you’d specify that inside the Post and Slug classes.

```php
/* inside Post.php */
  protected $has_one  = array('slug');

/* inside Slug.php */
  protected $belongs_to  = array('post');
```

In a 1:1 relationship we must specify each side of the relationship
slightly differently so that ActiveRecord knows the “direction” of the
relationship. We use belongs\_to for the model whose table contains the
foreign key (post\_id in this case). The other side of the relationship
uses has\_one. Since an object could have multiple 1:1 relationships, we
use an array to allow for additional tables. Notice the singular use of
slug and post. The code tries to read like English as much as possible,
so later when we do 1:many relationships you’ll plural strings. After
you’ve specified this relationship you can do some extra things with
your models. On every slug and post object you can now do →post and
→slug to get its post and slug respectively as an ActiveRecord object.
Also you set assign a slug or post using this mechanism. Furthermore, a
save will cascade to the relationship.

```php
$slug = Slug::find('first'); # SQL query to grab first slug
$slug->post; # an SQL query occurs behind the scenes to find the slug's post

$p = Post::find('first', array('include' => 'slug')); # SQL join
$p->slug; # no SQL query here because we already got this post's slug in the SQL join in the previous line

$p = Post::find('first');
$s = new Slug(array('slug' => 'super-slug'));
$p->slug = $s; # assign a slug to this post

$p->slug->slug = 'foobar';
$p->save(); # cascading save (post and slug are saved)
```

#### 1:many

In our example a post has many comments, but a comment only has one
post. Here’s how you’d specify it in the Post and Comment classes.

```php
/* inside Post.php */
  protected $has_many = array('comments');

/* inside Comment.php */
  protected $belongs_to = array('post');
```

Notice, we used plural “comments” for the has\_many and a singular
“post” for belongs\_to. Also notice how the comments table contains the
foreign key (post\_id) and therefore is a belongs\_to relationship. Once
we’ve done this Comment can do the same things as an 1:something
relationship can (see 1:1). Post now has some slight variations to the
features added in a 1:1 relationship. Now when accessing the attribute
comments you’d get an array of comment ActiveRecord objects that belong
to this Post.

```php
$p = Post::find('first');
echo $p->comments[0]->body;
```

You can also get the list of comment ids that belong to this post by
calling →comment\_ids. You can set the ids in a similar fashion.

```php
$p = Post::find('first');
$foo = $p->comment_ids;
# foo is now an array of comment ids that belong to this post
array_pop($foo); # pop off last comment id
array_push($foo, 23); # and another comment id to $foo

$p->comment_ids = $foo;
/* this will remove the comment we popped off of foo
    and add the comment we pushed onto foo to this post
*/
```

You can also push new objects onto the relationships.

```php
$c = new Comment(array('author' => 'anon', 'body' => 'first comment!!11'));
$p->comments_push($c); # this call saves the new comment and associates with this post
```

In this example, we might want to have comments destroyed when their
post is destroyed or when they are disassociated with their post. You
can have this happen by specifying the relationship slightly
differently. You can do this on any sort of relationship. Instead have
the following in the Post model.

```php
/* inside Post.php */
  protected $has_many = array(array('comments' => array('dependent' => 'destroy')));
```

#### many:many

A many:many relationship will have an intermediate table (and therefore
model) which ties two other tables together. In our example, there is a
many:many relationship between posts and categories. Our intermediate
table is categorizations. Here is how that is specified:

```php
/* inside Categorization.php */
  protected $belongs_to = array('post', 'category');

/* inside Post.php */
  protected $has_many = array(  'categorizations',
                          array('categories' => array('through' => 'categorizations')));

/* inside Category.php */
  protected $has_many = array(  'categorizations', 
                          array('posts' => array('through' => 'categorizations')));
```

Since the categorizations table contains the foreign keys post\_id and
category\_id, it has a belongs\_to relationship with those. The Post
model has a regular has\_many relationship with categorizations and a
special has\_many relationship with categories. We specify which table
that relationship goes through (categorizations), IOW which table is the
intermediate table of that relationship. The category to post
relationship is specified similarly. Posts and categories can now use
the special has\_many methods documented in the 1:many relationship.

### Working With Models

This section applies to all models regardless of any associations they
may have.

#### Create

```php
$p = new Post(array('title' => 'First Post!11!', 'body' => 'This is the body of my post'));
$p->save(); # saves this post to the table

$p2 = new Post();
$p2->title = "Second Post";
$p2->body = "This is the body of the second post";
$p2->save(); # save yet another post to the db
```

#### Retrieve

Retrieving data involves finding the rows you want to look at and
subsequently grabbing the column data as needed. The first parameter for
the find method should be one of the following:

-   an id number
-   an array of id numbers
-   the string “first”
-   the string “all”

When the first parameter is an id number or the string “first”, the
result will be an ActiveRecord object. Otherwise, it will be an array of
ActiveRecord objects. The find method takes quite a few different
options for its second parameter by using “named parameters” by
accepting an array of key, value pairs. You can pass it the following
keys with sane values:

-   limit
-   order
-   group
-   offset
-   select
-   conditions
-   include (for associations)

```php
$p = Post::find(1); # finds the post with an id = 1
$p->title; # title of this post
$p->body;  # body of this post

# returns the 10 most recent posts in an array, assuming you have a column called "timestamp"
$posts = Post::find('all', array('order' => 'timestamp DESC', 'limit' => 10));
```

#### Update

```php
$p = Post::find(1);
$p->title = "Some new title";
$p->save(); # saves the change to the post

# alternatively, the following is useful when a form submits an array
$_POST['post'] = array('title' => 'New Title', 'body' => 'New body here!');
$p = Post::find(1);
$p->update_attributes($_POST['post']); # saves the object with these attributes updated
```

#### Destroy

```php
$p = Post::find(1);
$p->destroy();
```

#### Hooks

The following hooks are available, just define the method of the same
name in the model that you want to use them:

-   before\_save
-   before\_create
-   after\_create
-   before\_update
-   after\_update
-   after\_save
-   before\_destroy
-   after\_destroy

#### Escaping Query Values

ActiveRecord will do proper escaping of query values passed to where
possible. However, it can’t do proper quoting when you do something like
the following.

```php
$p = Post::find('first', array('conditions' => "title = {$_GET['title']}"));
```

Instead you can use the quote static method to quote that value like so.

```php
$title = ActiveRecord::quote($_GET['title']);
$p = Post::find('first', array('conditions' => "title = $title"));
```

#### Manual Queries

Occasionally, though hopefully rarely, you may need to do specify some
queries by hand. You can use the query static method. This returns an
associative array with all the rows in it.

```php
ActiveRecord::query("SELECT COUNT(*) FROM bar as b1, bar as b2 where b2.id != b1.id");
```

### Table Structure For Example

```sql
--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `categorizations`
--

CREATE TABLE `categorizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(255) DEFAULT NULL,
  `body` text,
  `post_id` int(11) DEFAULT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `body` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `slugs`
--

CREATE TABLE `slugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) DEFAULT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
```