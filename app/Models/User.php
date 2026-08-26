<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class User extends  Authenticatable
{
	use HasFactory, Notifiable;
	protected $table = 'users';

	protected $casts = [
		'email_verified_at' => 'datetime'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'email',
		'email_verified_at',
		'password',
		'phone',
		'remember_token',
		'staff_id',
        'user_cat',
		'status',
		'department_id',
	];

	public function getUserCategory (){

		return UserCat::find($this->user_cat)->cat_name;
	}

       public function categoryname()
{
	return $this->belongsTo(UserCat::class, 'user_cat', 'cat_id'); // Adjust if your FK is different
}

	public function getUserName ($id){

		if(User::where('id',$id)->get()->count() > 0){

			return User::find($id)->name;

		}else{

			return 'Not Available';
		}

		
	}

	public function staff()
{
    return $this->belongsTo(\App\Models\Staff::class, 'staff_id', 'staff_id');
}

	public function getStoreIds(): array
	{
		if (empty($this->department_id)) {
			return [];
		}

		return array_values(array_filter(array_map(
			'intval',
			explode('~', $this->department_id)
		)));
	}

	public function category()
	{
		return $this->belongsTo(UserCat::class, 'user_cat', 'cat_id');
	}

	public function extraLinks()
	{
		return $this->hasMany(UserExtraLink::class, 'user_id');
	}

	public function getAccessibleLinkIds(): array
	{
		$roleLinks = UserCatLink::where('cat_id', $this->user_cat)->pluck('link_id')->all();
		$extraLinks = UserExtraLink::where('user_id', $this->id)->pluck('link_id')->all();

		return array_values(array_unique(array_map('intval', array_merge($roleLinks, $extraLinks))));
	}

	public function canAccessLinkRoute(string $routeName): bool
	{
		$accessibleLinkIds = $this->getAccessibleLinkIds();

		if (empty($accessibleLinkIds)) {
			return false;
		}

		return UserLink::whereIn('link_id', $accessibleLinkIds)
			->where('status', 'Active')
			->where('link_url', $routeName)
			->exists();
	}

	public function canAccessScreen(string $pageIdSub): bool
	{
		$accessibleLinkIds = $this->getAccessibleLinkIds();

		if (empty($accessibleLinkIds)) {
			return false;
		}

		return UserLink::whereIn('link_id', $accessibleLinkIds)
			->where('status', 'Active')
			->where('page_id_sub', $pageIdSub)
			->exists();
	}
}
