<?php

abstract class BaseModel extends CActiveRecord
{
	protected $hasBlocks = array();
	protected $hasContent = array();

	protected $hasMany = array();
	protected $hasOne = array();
	protected $belongsTo = array();

	protected $attributes = array();

	/**
	 * Returns an instance of the specified model
	 *
	 * @param string $class
	 *
	 * @return object The model instance
	 * @static
	*/
	public static function model($class = __CLASS__)
	{
		return parent::model($class);
	}

	/**
	 * @return bool Whether this model has content (joined to blx_content via blx_blocksmodelclass_content)
	 */
	public function getHasContent()
	{
		return $this->hasContent;
	}

	/**
	 * @return bool Whether this model has custom blocks (joined to blx_contentblocks via blx_blocksmodelclass_blocks)
	 */
	public function getHasBlocks()
	{
		return $this->hasBlocks;
	}

	/**
	 * @return array The model's one-to-many relationships
	 */
	public function getHasMany()
	{
		return $this->hasMany;
	}

	/**
	 * @return array The model's one-to-one relationships
	 */
	public function getHasOne()
	{
		return $this->hasOne;
	}

	/**
	 * @return array One-to-many or one-to-one relationships
	 */
	public function getBelongsTo()
	{
		return $this->belongsTo;
	}

	/**
	 * @return array The model's non-relational attributes
	 */
	public function getAttributes($names = true)
	{
		return $this->attributes;
	}

	/**
	 * @return string The associated database table name
	 */
	public function tableName()
	{
		return '{{'.strtolower(get_class($this)).'}}';
	}

	/**
	 * @return array Validation rules for model's attributes
	 */
	public function rules()
	{
		$attributes = $this->getAttributes();

		$required = array();
		$integers = array();
		$maxSizes = array();

		$defaultAttributeSettings = array('type' => AttributeType::String, 'maxSize' => 150, 'required' => false);

		foreach ($attributes as $attributeName => $attributeSettings)
		{
			$attributeSettings = array_merge($defaultAttributeSettings, $attributeSettings);

			if ($attributeSettings['required'] === true)
				$required[] = $attributeName;

			if ($attributeSettings['type'] == AttributeType::Integer)
				$integers[] = $attributeName;

			if ($attributeSettings['type'] == AttributeType::String)
				$maxSizes[(string)$attributeName['maxSize']][] = $attributeName;
		}

		$rules = array();

		if ($required)
			$rules[] = array(implode(', ', $required), 'required');

		if ($integers)
			$rules[] = array(implode(', ', $integers), 'numerical', 'integerOnly' => true);

		if ($maxSizes)
		{
			foreach ($maxSizes as $maxSize => $attributeNames)
			{
				$rules[] = array(implode(', ', $attributeNames), 'length', 'max' => (int)$maxSize);
			}
		}

		$rules[] = array(implode(', ', array_keys($attributes)), 'safe', 'on' => 'search');

		return $rules;
	}

	/**
	 * @return array Relational rules
	 */
	public function relations()
	{
		$relations = array();

		foreach ($this->getHasBlocks() as $key => $settings)
		{
			$relations[$key] = $this->generateJoinThroughRelation('ContentBlocks', 'block_id', $settings);
		}

		foreach ($this->getHasContent() as $key => $settings)
		{
			$relations[$key] = $this->generateJoinThroughRelation('Content', 'content_id', $settings);
		}

		foreach ($this->getHasMany() as $key => $settings)
		{
			$relations[$key] = $this->generateHasXRelation(self::HAS_MANY, $settings);
		}

		foreach ($this->getHasOne() as $key => $model)
		{
			$relations[$key] = $this->generateHasXRelation(self::HAS_ONE, $settings);
		}

		foreach ($this->getBelongsTo() as $key => $model)
		{
			$relations[$key] = array(self::BELONGS_TO, $model, $key.'_id');
		}

		return $relations;
	}

	/**
	 * Generates HAS_MANY relations to a model through another model
	 * @param string $model The destination model
	 * @param string $fk2 The join table's foreign key to the destination model
	 * @param array $settings The initial model's settings for the relation
	 * @return The CActiveRecord relation
	 * @access protected
	 */
	protected function generateJoinThroughRelation($model, $fk2, $settings)
	{
		return array(self::HAS_MANY, $model, array($settings['foreignKey'].'_id' => $fk2), 'through' => $settings['through']);
	}

	/**
	 * Generates HAS_MANY and HAS_ONE relations
	 * @param string $relationType The type of relation to generate (self::HAS_MANY or self::HAS_ONE)
	 * @param array $settings The relation settings
	 * @return array The CActiveRecord relation
	 * @access protected
	 */
	protected function generateHasXRelation($relationType, $settings)
	{
		if (is_array($settings['foreignKey']))
		{
			$fk = array();
			foreach ($settings['foreignKey'] as $fk1 => $fk2)
			{
				$fk[$fk1.'_id'] = $fk2.'_id';
			}
		}
		else
		{
			$fk = $settings['foreignKey'].'_id';
		}

		$relation = array($relationType, $settings['model'], $fk);

		if (isset($settings['through']))
			$relation['through'] = $settings['through'];

		return $relation;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider The data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		foreach ($this->getAttributes() as $attributeName => $attributeSettings)
		{
			$criteria->compare($attributeName, $this->$attributeName);
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria
		));
	}

	/**
	 * Creates the table(s) necessary for this model to save its data
	 * @static
	 */
	public static function install()
	{
		
	}
}
