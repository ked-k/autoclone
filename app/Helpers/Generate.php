<?php

namespace App\Helpers;

use App\Models\Participant;
use App\Models\Sample;
use App\Models\SampleReception;

class Generate
{
    public static function password($length = 2)
    {
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomNumber = '';
        $randomSymbol = '';
        $randomUppercase = '';
        $randomLowercase = '';
        for ($i = 0; $i < $length; $i++) {
            $randomNumber .= $numbers[rand(0, strlen($numbers) - 1)];
            $randomSymbol .= $symbols[rand(0, strlen($symbols) - 1)];
            $randomUppercase .= $uppercase[rand(0, strlen($uppercase) - 1)];
            $randomLowercase .= $lowercase[rand(0, strlen($lowercase) - 1)];
        }

        return str_shuffle($randomNumber.$randomSymbol.$randomUppercase.$randomLowercase);
    }

    public static function batchNo()
    {
        $date = date('dmY');
        $batch_no = '';
        $latestBatchNo = SampleReception::select('batch_no')->orderBy('id', 'desc')->first();

        if ($latestBatchNo) {
            $batchNumberSplit = explode('-', $latestBatchNo->batch_no);
            $number = ((int) filter_var($batchNumberSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1);
            $batch_no = $date.'SB-'.$number;
        } else {
            $batch_no = $date.'SB-1';
        }

        return $batch_no;
    }

    public static function participantNo()
    {
        $participant_no = '';
        $yearStart = date('y');
        $latestParticipantNo = Participant::select('participant_no')->orderBy('id', 'desc')->first();

        if ($latestParticipantNo) {
            $participantNumberSplit = explode('-', $latestParticipantNo->participant_no);
            $participantNumberYear = (int) filter_var($participantNumberSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($participantNumberYear == $yearStart) {
                $participant_no = $participantNumberSplit[0].'-'.str_pad(((int) filter_var($participantNumberSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 3, '0', STR_PAD_LEFT).'P';
            } else {
                $participant_no = 'GMI'.$yearStart.'-001P';
            }
        } else {
            $participant_no = 'GMI'.$yearStart.'-001P';
        }

        return $participant_no;
    }

    public static function sampleNo()
    {
        $sample_no = '';
        $yearStart = date('y');
        $latestSampleNo = Sample::select('sample_no')->orderBy('id', 'desc')->first();

        if ($latestSampleNo) {
            $sampleNumberSplit = explode('-', $latestSampleNo->sample_no);
            $sampleNumberYear = (int) filter_var($sampleNumberSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($sampleNumberYear == $yearStart) {
                $sample_no = $sampleNumberSplit[0].'-'.str_pad(((int) filter_var($sampleNumberSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 3, '0', STR_PAD_LEFT).'S';
            } else {
                $sample_no = 'GMI'.$yearStart.'-001S';
            }
        } else {
            $sample_no = 'GMI'.$yearStart.'-001S';
        }

        return $sample_no;
    }

    public static function labNo()
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lettersLength = strlen($letters);
        $labno = '';
        $year = date('y');

        $latestLabNo = Sample::select('lab_no')->orderBy('id', 'desc')->first();

        if ($latestLabNo) {
            $yearPart = substr($latestLabNo->lab_no, 0, 2);
            $letterPart = substr($latestLabNo->lab_no, 2, 5);
            $numPart = (int) substr($latestLabNo->lab_no, 5);

            if ($yearPart == $year) {
                $letter1 = $letterPart[0];
                $letter2 = $letterPart[1];
                $letter3 = $letterPart[2];

                if ($letter3 != $letters[$lettersLength - 1]) {
                    if ($numPart == 999) {
                        $letter3 = $letters[strpos($letters, $letter3) + 1];
                    } else {
                        $letter3 = $letter3;
                        $letter2 = $letter2;
                        $letter1 = $letter1;
                    }
                } else {
                    if ($letter2 != $letters[$lettersLength - 1]) {
                        if ($numPart == 999) {
                            $letter2 = $letters[strpos($letters, $letter2) + 1];
                            $letter3 = $letters[0];
                        } else {
                            $letter2 = $letter2;
                            $letter1 = $letter1;
                        }
                    } else {
                        if ($letter1 != $letters[$lettersLength - 1]) {
                            if ($numPart == 999) {
                                $letter1 = $letters[strpos($letters, $letter1) + 1];
                                $letter2 = $letters[0];
                                $letter3 = $letters[0];
                            } else {
                                $letter1 = $letter1;
                            }
                        } else {
                            if ($numPart == 999) {
                                return $labno;
                            } else {
                                $letter1 = $letter1;
                                $letter2 = $letter2;
                                $letter3 = $letter3;
                            }
                        }
                    }
                }

                $numPart == 999 ? $numPart = 1 : $numPart++;
                $numPart < 10 || $numPart < 100 ? $numPart = str_pad($numPart, 3, '0', STR_PAD_LEFT) : $numPart;
                $labno = $year.$letter1.$letter2.$letter3.$numPart;
            } else {
                $labno = $year.'AAA001';
            }
        } else {
            $labno = $year.'AAA001';
        }

        return $labno;
    }
}
