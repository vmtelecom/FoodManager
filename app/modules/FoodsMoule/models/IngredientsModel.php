<?php
namespace FoodsModule;

class IngredientsModel extends \BaseTableAccessModel
{
	/**
	 * @var string 
	 */
	protected $table = 'ingredients';
	
	/**
	 * Get ingredient by name, create new ingredient and return it if it does not yet exists
	 * 
	 * @param string $name 
	 * 
	 * @return \Nette\Database\Table\ActiveRow 
	 */
	public function createIngredientIfNotExists($ingredient_name)
	{		
		$condition = array(
			'match' => \Nette\Utils\Strings::webalize($ingredient_name, ' '),
		);
		
		$result = $this->database->table($this->table)->where($condition);
		$count = $result->count();
		
		if ($count === 1) {
			$ingredient = $result->fetch();
		} elseif ($count > 1) {
			//match against full tag name if possible
			$full_match_result = $this->database->table($this->table)->where(array('ingredient' => $ingredient_name));
			if ($full_match_result->count()) {
				$ingredient = $full_match_result->fetch();
			} else {
				$ingredient = $result->fetch();
			}
		} elseif ($count === 0) {
			$condition['ingredient'] = $ingredient_name;
			$ingredient = $this->createOne($condition);
		} else {
			throw new \Exception('Negative ingredient count on select!');
		}
		
		
		return $ingredient;
	}
	
	/**
	 * Gets categories
	 * 
	 * @return \Nette\Database\Table\Selection 
	 */
	public function getCategories()
	{
		return $this->database->table('tags')
				->select('tags.*, count(foods_tags:id_tag) AS "number_of_foods"')
				->where('is_category = ?', TRUE)
				->group('id_tag');
	}
}
