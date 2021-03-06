<?php namespace App\Http\Requests;

use App\Districts;
use App\Http\Requests\Request;
use App\States;
use App\Villages;
use Illuminate\Support\Facades\Auth;

class ParticipantsFormRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$this->current_user = Auth::user();

		//die($user_id .''. $current_user_id .''. var_dump($this->current_user->allowed('edit.user',$user )));

		if ( $this->current_user->is('admin') ){
			return true;
		}elseif ($this->current_user->level() > 6 )
		{
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		//$url_segments = Request::segments();
		//$update_user_id = $url_segments[2];
		/** 'name' => 'Sithu Thwin',
		 * 'email' => 'sithu@thwin.net',
		 * 'nrc_id' => '5/SaKaNa(N)156828',
		 * 'ethnicity' => '',
		 * 'state' => '101',
		 * 'user_gender' => 'male',
		 * 'dob' => '2015-03-13',
		 * 'user_line_phone' => '',
		 * 'user_mobile_phone' => '09797969695',
		 * 'user_mailing_address' => '',
		 * 'education_level' => 'none',
		 * 'user_biography' =>

		 **/

		$method = Request::method();
		//die($method);
		if('PATCH' == $method){
			$rules = [
				//
				'name' => 'required|min:4',
				'email' => 'exists:participants|email',
				'nrc_id' => 'exists:participants',
				'user_gender' => 'required',
				'dob' => 'dateformat:d-m-Y',
				'user_line_phone' => '',
				'user_mobile_phone' => 'required',
				'user_mailing_address' => 'required',
				'education_level' => '',
				'current_org' => '',
				'user_biography' => '',
				'participant_type' => '',
				'location'
			];
		}else {
			$rules = [
				//
				'name' => 'required:min:4',
				'email' => 'required|unique:participants|email',
				'nrc_id' => 'required|unique:participants',
				'user_gender' => 'required',
				'dob' => 'dateformat:d-m-Y',
				'user_line_phone' => '',
				'user_mobile_phone' => 'required',
				'user_mailing_address' => 'required',
				'education_level' => '',
				'current_org' => '',
				'user_biography' => '',
				'participant_type' => '',
				'location'
			];
		}

		return $rules;
	}


	// Here we can do more with the validation instance...
	public function dataentryValidation($validator){

		// Use an "after validation hook" (see laravel docs)
		$validator->after(function($validator)
		{
			$participant_type = $this->input('participant_type');

			if($participant_type == 'coordinator'){
				try {
					$state = States::where('state', '=', $this->input('location'))->pluck('state');
				}catch (\Exception $e){
					$validator->errors()->add('participant_type', 'No state with that name. Check again!');
				}
				if(empty($state)){
					$validator->errors()->add('participant_type', 'Your location not match with state record, Check again!');
				}

			}else{
				try {
					$village = Villages::where('village', '=', $this->input('location'))->pluck('village');
				}catch (\Exception $e){
					$validator->errors()->add('participant_type', 'No state with that name. Check again!');
				}
				if(empty($village)){
					$validator->errors()->add('participant_type', 'Your location not match with village record, Check again!');
				}
			}

		});
	}

}
