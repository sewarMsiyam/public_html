<?php

if (!defined('CHECK_ME')) { exit; }

class category_item
{
    var $cat_id;
    var $depth;
    var $cat_title;
    var $cat_parent_id;
    var $cat_long_title;

    function category_item($cat_id, $depth, $cat_title, $cat_parent_id, $cat_long_title = '')
    {
        $this->cat_id = $cat_id;
        $this->depth = $depth;
        $this->cat_title = $cat_title;
        $this->cat_parent_id = $cat_parent_id;
        $this->cat_long_title = $cat_long_title;
    }
}

/*************************************************************/
// Builds the category list for the category select

$list = array();

function build_list($cat_array, $item = '', $depth = 0)
{
    global $template;
    global $list;

    foreach($cat_array as $category)
    {
        $loop_item = "$item$category[cat_title]";

        $category_item = new category_item($category[cat_id], $depth, $category[cat_title], $category[cat_parent_id], $loop_item);
        $list[] = $category_item;

        if(count($category[children]) > 0)
        {
            $depth++;
            build_list($category[children], $loop_item." > ", $depth);
            $depth--;
        }

        $loop_item = '';
    }

    return $list;
}

/*************************************************************/
// Adds the object to the children array of the object with 
// a cat_id equal to $parent_id

function tree_add($tree, $parent_id, $object, $cat_id)
{
    // Only start from the given cat_id, ignore all other roots

    if($parent_id == '0' and $object[cat_id] == $cat_id)
    {
        $tree[$object[cat_id]] = $object;
        return $tree;
    }

    if($tree)
    {
        foreach($tree as $key => $value)
        {
            $current = $tree[$key];

            // If this is the parent, add the object to it's children array
            if($current[cat_id] == $parent_id)
            {
                $tree[$key][children][$object[cat_id]] = $object;
            }
            else
            {
                // If it's not in this level, look a level deeper on the current object.
                $tree[$key][children] = tree_add($current[children], $parent_id, $object, $cat_id);
            }
        }
    }

    return $tree;
}


$result = $intro->db->query("select * from maa_product_cat order by catid asc;");

while($category = $intro->db->fetch_assoc($result))
{
    $children = array();

    $category[children] = $children;

    $cat_id = $category[catid];
    $cat_parent_id = $category[father];

    $cat_tree = tree_add($cat_tree, $cat_parent_id, $category, $cat_id);
}

$cat_list = build_list($cat_tree);

	//from wordpress
	function get_category_parents( $id, $link = false , $separator=">" ) {
		global $intro;

		$chain = '';
		$id = intval($id);
		$result = $intro->db->query("SELECT catid, catname, father FROM ".PREFIX."_product_cat WHERE catid=$id");
		$parent = $intro->db->fetch_assoc($result);

		if ( $parent['father'] && ( $parent['father'] != $parent['catid'] ) ) 
		{
			$chain .= get_category_parents( $parent['father'], $link ,$separator );
		}

		if ($link){
			$chain .= "<a href=\"{$parent['catid']}\">$name</a>".$separator;
		}else{
			$chain .= $parent['catname'].$separator;
		}	
		return $chain;
	}
	//from opencart
	function getCategoriesByParentId($category_id)
	{
		global $intro;
		
		$category_data = array();
		$sql = $intro->db->query("SELECT catid, catname, father FROM ".PREFIX."_product_cat WHERE father=$category_id;");
		while ($row = $intro->db->fetch_assoc($sql))
		{
			$category_data[] = array(
				'catid' => $row['catid'],
				'catname' => $row['catname'],
				'father' => $row['father']
			);
			$children = $this->getCategoriesByParentId($row['catid']);
			if ($children) {
				$category_data = array_merge($children, $category_data);
			}           
		}

		return $category_data;
	}
?>