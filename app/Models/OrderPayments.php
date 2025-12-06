<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class OrderPayments extends Authenticatable
{
    use  HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		"order_id",
        "type",
		"payment_type",
		"payment_method",
		"amount",
		"comments",
		"comment",
    ];

    protected $UpdatableFields = [ ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
	
	public function GetRecordById($id){
		return $this::where('id', $id)->first();
	}
	
	public function GetRecords($conditions = []){
		$query = $this::query();
		foreach($conditions as $key => $value){
			$query->where($key, $value);
		}
		return $query->get();
	}
	
	public function UpdateRecord($Details){
		$Record = $this::where('id', $Details['id'])->update($Details);
		return true;
	}
	public function CreateRecord($Details){
		$Record = $this::create($Details);
		return $Record;
	}


}