<?php
$appear = '';

class Lead_Card implements JsonSerializable
{

	private $id, $name, $contact_no, $email, $query, $category, $location, $date_time, $isUnlocked, $isHidden;

	function __construct($id, $name, $contact_no, $email, $query, $category, $location, $date_time, $isUnlocked = false, $isHidden = false)
	{
		$this->create_card($id, $name, $contact_no, $email, $query, $category, $location, $date_time, $isUnlocked ? true : false, $isHidden ? true : false);
	}

	public function isHidden()
	{
		return $this->isHidden;
	}

	public function setHidden($x)
	{
		$this->isHidden = $x;
	}

	public function isUnlocked()
	{
		return $this->isUnlocked;
	}

	public function setUnlocked($x)
	{
		$this->isUnlocked = $x;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($x)
	{
		$this->id = $x;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setEmail($x)
	{
		$this->email = $x;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getContactNo()
	{
		return $this->contact_no;
	}

	public function setContactNo($x)
	{
		$this->contact_no = $x;
	}

	public function setName($x)
	{
		$this->name = $x;
	}

	public function getLocation()
	{
		return $this->location;
	}

	public function setLocation($location_id)
	{
		$location_data = get_term_by('id', $location_id, 'locations');
		$leads_location = $location_data->name;
		$this->location = $leads_location;
	}

	public function getCategory()
	{
		return $this->category;
	}

	public function setCategory($category_id)
	{
		$category_data = get_term_by('id', $category_id, 'listing_categories');
		$leads_category = $category_data->name;
		$this->category = $leads_category;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function setQuery($x)
	{
		$this->query = $x;
	}

	public function getDateTime()
	{
		return $this->date_time;
	}

	public function setDateTime($x)
	{
		$this->date_time = $x;
	}

	private function create_card($_id, $_name, $_contact_no, $_email, $_query, $_category, $_location, $_date_time, $_isUnlocked, $_isHidden)
	{
		$this->setId($_id);
		$this->setName($_name);
		$this->setContactNo($_contact_no);
		$this->setEmail($_email);
		$this->setQuery($_query);
		$this->setCategory($_category);
		$this->setLocation($_location);
		$this->setDateTime($_date_time);
		$this->setUnlocked($_isUnlocked);
		$this->setHidden($_isHidden);
	}

	public function edu_shortcode($appear)
	{
		include 'html/lead-portal.html';
		return null;
	}

	public function jsonSerialize()
	{
		return [
			'lead_card' => [
				'leadId' => $this->getId(),
				'name' => $this->getName(),
				'contact_no' => $this->getContactNo(),
				'email' => $this->getEmail(),
				'query' => $this->getQuery(),
				'category' => $this->getCategory(),
				'location' => $this->getLocation(),
				'date_time' => $this->getDateTime(),
				'isUnlocked' => $this->isUnlocked(),
				'isHidden' => $this->isHidden()
			]
		];
	}

}

$shrt_code1 = new Lead_Card('Rohit', 'Lucknow', 'CEO', 'Nirvana', '', '', '', '');
//$shrt_code2 = new Lead_Card('Anantharam', 'Chennai', 'CTO', 'Life');
add_shortcode('edugorilla_leads', array($shrt_code1, 'edu_shortcode'));


?>