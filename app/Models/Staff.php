<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Staff
 * 
 * @property int $staff_id
 * @property string|null $surname
 * @property string|null $firstname
 * @property string|null $othername
 * @property string|null $gender
 * @property string|null $service_no
 * @property string|null $year
 * @property string|null $rank_class
 * @property string|null $rank
 * @property string|null $region_id
 * @property string|null $district_id
 * @property string|null $station
 * @property string|null $department
 * @property string|null $unit
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $res_address
 * @property string|null $dob
 * @property string|null $marital_status_id
 * @property string|null $picture
 *
 * @package App\Models
 */
class Staff extends Model
{
	protected $table = 'staff';
	protected $primaryKey = 'staff_id';
	public $timestamps = false;

	protected $fillable = [
		'surname',
		'firstname',
		'othername',
		'gender',
		'service_no',
		'year',
		'rank_class',
		'rank',
		'region_id',
		'district_id',
		'station',
		'department',
		'unit',
		'phone',
		'email',
		'res_address',
		'dob',
		'marital_status_id',
		'picture'
	];

	 
}
