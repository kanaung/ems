<?php namespace App\Http\Controllers;

use App\Districts;
use App\EmsForm;
use App\EmsFormQuestions;
use App\EmsQuestionsAnswers;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Participant;
use App\States;
use App\Townships;
use App\User;
use App\Villages;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class AjaxController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
		$this->current_user_id = Auth::id();
		$this->auth_user = User::find($this->current_user_id);
	}

	public function check(Request $request){

		$form_url = $request->route('form');

		//$form_name = urldecode($form_url);
		if ($this->auth_user->can("add.data")) {

			$form_name = urldecode($form_url);

			$form = EmsForm::where('name', '=', $form_name)->get();

			$form_id = $form->first()['id'];
			$form_type = $form->first()['type'];
			if ($form_type == 'spotchecker') {
				$enu_form_id = Input::get('enu_form_id');
				if (strlen($enu_form_id) != 7) {

					$ajax_response['status'] = false;
					$ajax_response['message'] = '<p class="text-red">Enumerator Form ID need to be exactly 7 digits</p>';                //	break;

				}else{
				try {
					$answer_id = EmsQuestionsAnswers::where('interviewee_id', '=', $enu_form_id)->pluck('id');
					$answer = EmsQuestionsAnswers::find($answer_id);
					$participant = Participant::find($answer->participant->id);

					$enu_answers = $answer->answers;
					$enu_form_id = $answer->form_id;
					//var_dump($enu_answer);
					//var_dump($enu_form_id);
					$q_a = array();
					$spotchecker_questions = EmsFormQuestions::where('q_type','spotchecker')->lists('parent_id');
					foreach($spotchecker_questions as $q_id){
						$question = EmsFormQuestions::find($q_id);

						$question_number = $question->question_number;

						if(in_array($question->q_type, array('same','sub'))){
							foreach($question->get_parent->answers as $ans_k => $ans_v){
								if ($ans_v['value'] === $enu_answers[$q_id]) {
									$q_a['q-'.$q_id] = '('.$ans_v['value'].') '.$ans_v['text'];
								}
							}
						}else{
							foreach($question->answers as $ans_k => $ans_v){
								if ($ans_v['value'] === $enu_answers[$q_id]) {
									$q_a['q-'.$q_id] = '('.$ans_v['value'].') '.$ans_v['text'];
								}
							}
						}

					}

					//dd($q_a);




				} catch (\ErrorException $e) {

				}
					foreach($participant->parents as $parent){
						if($parent->participant_type == 'coordinator'){
							$coordinator = $parent;
						}
						if($parent->participant_type == 'spotchecker'){
							$spotchecker = $parent;
						}
					}
				if (!isset($spotchecker)){
					$ajax_response['status'] = false;
					$ajax_response['message'] = '<p class="text-red">Spot Checker not found!</p>';
					echo json_encode($ajax_response);
					return;
				}else{
					$q_a['spotchecker_id'] = $spotchecker->participant_id;
					$q_a['spotchecker_name'] = $spotchecker->name;
				}
				if (isset($answer) && !empty($answer)) {

					$participant_name = $answer->participant->name;
					$village = $participant->villages->first()->village;
					$q_a['enu_name'] = $participant_name;
					$q_a['village'] = $village;
					$ajax_response['status'] = true;
					$ajax_response['message']= $q_a;
					//$ajax_response['enu_name'] = $participant_name;
					//$ajax_response['village'] = $village;
				} else {
					$ajax_response['status'] = false;
					$ajax_response['message'] = '<p class="text-red">Enumerator Form not found!</p>';
				}

			}

			}else{

			$interviewer_id = Input::get('interviewer_id');
			$interviewee_id = Input::get('interviewee_id');

				if (strlen($interviewer_id) != 6) {

					$ajax_response['status'] = false;
					$ajax_response['message'] = '<p class="text-red">Enumerator ID need to be exactly 6 digits</p>';                //	break;

				} else {

					preg_match('/([0-9]{3})([0-9]{3})/', $interviewer_id, $matches);

					if (empty($matches)) {

						$ajax_response['status'] = false;
						$ajax_response['message'] = '<p class="text-red">Wrong Enumerator ID</p>';
					}

					try {
						$state = States::where('state_id', '=', (int)$matches[1]);
					} catch (\Exception $e) {
						$ajax_response['status'] = false;
						$ajax_response['message'] = '<p class="text-red">No state with ID ' . $matches[1] . '. Check again Enumerator ID!</p>';
					}
					try {
						$village_id = Villages::where('village_id', '=', (int)$matches[2])->first()['id'];

						$village = Villages::find($village_id);

						$township = Townships::find($village->township->id);

						$district = Districts::find($township->district->id);

						$state_for_village = States::find($district->state->id);

						$enumerator = $village->enumerator->first()->name;


					} catch (\Exception $e) {
						$ajax_response['status'] = false;
						$ajax_response['message'] = '<p class="text-red">No village with ID ' . $matches[2] . '. Check again Enumerator ID!</p>';
					}

					if (isset($interviewee_id)) {
						$answers_id = $interviewer_id . $interviewee_id;
						$answer = EmsQuestionsAnswers::where('interviewee_id', '=', $answers_id)->pluck('interviewee_id');

						if (!empty($answer)) {
							$ajax_response['status'] = false;
							$ajax_response['message'] = '<p class="text-red">Data already exists.</p>';
							echo json_encode($ajax_response);
							return;
						}
					}


					if (isset($state_for_village) && !empty($state_for_village)) {
						if ($state_for_village->state_id != (int)$matches[1]) {
							$ajax_response['status'] = false;
							$ajax_response['message'] = '<p class="text-red">' . _t(':village does not exist in :state. Check again Enumerator ID!', array('village' => $village->village, 'state' => $state->first()["state"])) . '</p>';
						} elseif (isset($enumerator)) {
							$ajax_response['status'] = true;
							$response['enumerator'] = $enumerator;
							$response['state'] = $state->first()["state"];
							$response['village'] = $village->village;
						} else {
							$ajax_response['status'] = false;
							$ajax_response['message'] = '<p class="text-red">_t("Enumerator cannot find in database!")</p>';
						}
					}



			}
				if(isset($response))
				$ajax_response['message'] = $response;

			//echo 'true';

		}

		} else {
			$ajax_response['status'] = false;
			$ajax_response['message'] = 'User has no permission to add data';
		}

		echo json_encode($ajax_response);

	}

	public function sort(Request $request)
	{
		$form_url = $request->route('form');

		//$form_name = urldecode($form_url);
		if ($this->auth_user->can("create.form")) {

			$form_name = urldecode($form_url);
			try {
				$getform = EmsForm::where('name', '=', $form_name)->get();
				$form_array = $getform->toArray();
			}catch (QueryException $e){
				$form_array = '';
			}
			//return $form->toArray();

			if (empty($form_array)) {
				$getform = EmsForm::find($form_url);
				$id = $getform->id;
				$form_name = $getform->name;
				$form_answers_count = $getform->no_of_answers;
			} elseif (!empty($form_array)) {
				$id = $getform->first()['id'];
				$form_answers_count = $getform->first()['no_of_answers'];
			} else {
				return view('errors/404');
			}

			if(Input::has('listid'))
			{
				$i = 1;
				foreach(Input::get('listid') as $qid)
				{
					//$question = EmsFormQuestions::find($id);
					$question = EmsFormQuestions::firstOrNew(array('id' => $qid,'form_id' => $id, 'list_id' => $i));
					$question->list_id = $i;
					$question->save();
					$i++;
				}
				return Response::json(array('success' => true));
			}
			else
			{
				return Response::json(array('success' => false));
			}


		}

	}

}
