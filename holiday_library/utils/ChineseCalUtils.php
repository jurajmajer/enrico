<?php

class ChineseCalUtils {

	public function calculateChineseCalendar($year) {
		
		$phaset = array();
		$fdm=0;
		$wsm = 0;
		$icm=0;
		$zqi=270;
		$zy=0;
		$minx=1;
		
		$k = floor(($year - 1900) * 12.3685) - 1;
		$synMonth = 29.53058868;
		for ($l = 0; $l < 16; $l++) {
			$t = $k / 1236.85;
			$t2 = $t * $t;
			$t3 = $t2 * $t;
			$mtime = 2415020.75933  + $synMonth * $k + 0.0001178 * $t2
						 - 0.000000155 * $t3 + 0.00033 * $this->dsin(166.56 + 132.87 * $t - 0.009173 * $t2);
			$m = 359.2242    + 29.10535608 * $k  - 0.0000333 * $t2  - 0.00000347 * $t3;
			$mprime = 306.0253    + 385.81691806 * $k + 0.0107306 * $t2   + 0.00001236 * $t3;
			$f = 21.2964     + 390.67050646 * $k  - 0.0016528 * $t2  - 0.00000239 * $t3;
			$mtime +=     (0.1734 - 0.000393 * $t) * $this->dsin($m)  + 0.0021 * $this->dsin(2 * $m) - 0.4068 * $this->dsin($mprime)
							   + 0.0161 * $this->dsin(2 * $mprime) - 0.0004 * $this->dsin(3 * $mprime) + 0.0104 * $this->dsin(2 * $f)
							   - 0.0051 * $this->dsin($m + $mprime) - 0.0074 * $this->dsin($m - $mprime) + 0.0004 * $this->dsin(2 * $f + $m)
							   - 0.0004 * $this->dsin(2 * $f - $m) - 0.0006 * $this->dsin(2 * $f + $mprime)  + 0.0010 * $this->dsin(2 * $f - $mprime)
							   + 0.0005 * $this->dsin($m + 2 * $mprime);
			$ctg= ($mtime - floor($mtime)) > (1/6) ? (floor($mtime) + (1/6)) : (floor($mtime) - 1 + (1/6));
			$T = ($ctg - 2415020) / 36525;
			$K = 2 * pi() / 360; 
			$M = 358.475833 + 35999.04975 * $T - 0.00015 * $T * $T - 0.00000333333 * $T * $T * $T;
			$K1 = $K * $M;
			$K2 = $K * 2 * $M;
			$K3 = $K * 3 * $M;
			$L = 279.6966778 + 36000.768925 * $T + 0.0003025 * $T * $T
				+(1.91946028 * sin($K1)) + (0.02009389 * sin($K2)) + (0.000292778 * sin($K3))
				-((0.0047889 * sin($K1) + 0.000100278 * sin($K2) + 0.000000278 * sin($K3)) * $T)
				-(0.000014444 * sin($K1) * $T * $T);
			while ($L >= 360) { $L = $L - 360; }
			while ($L < 0 ) { $L = $L + 360;}
			$Om0 = (125.04 - 1934.1 * $T) / 360;
			if ($Om0 > 0) {
				$Om0 = floor($Om0);
			} 
			else {
				$Om0 = ceil($Om0);
			}
			
			$Om1 = 360 *((125.04 - 1934.1 * $T) / 360) - $Om0;
			if ($Om1<0) $Om1= $Om1 + 360;
			$L = $L - 0.00569 - 0.00478 * sin(pi() / 180 * $Om1);
			while ($L >= 360) {$L = $L - 360;}
			while ($L < 0) {$L = $L + 360;}
			if ($wsm == 0 && $L < 270) $fdm = $mtime;
			if ($wsm == 0 && $L > 270) {
				$wsm = 1;
				$minx = 1;
				$phaset[0] =  $this->jtog($fdm + (1/3));
			}
			if ($wsm == 1) {
				if ($icm == 0 && $L < $zqi) $icm = $minx - 1;
				if ($icm > 0 && $minx == 13 && $L > 270) $icm = 0;	// icm = intercalary month
				if ($L > 330) {
				$zy = ($minx - 1);
				}
				if ($minx >= 0) $phaset[$minx] = $this->jtog ($mtime + (1/3));
				$zqi = $zqi + 30;
				if ($zqi > 330) $zqi=0;
				if ($minx > 10 && $zqi== 330 ){
					break;
				}
			}
			$k += 1;
			$minx += 1;
		}
		
		$retVal = array();
		$totalCount = 0;
		$index1 = 0;
		$index2 = $zy;
		while($totalCount < 12 && $index2 < count($phaset))
		{
			if($icm == 0 || $icm != $index2)
			{
				$retVal[$index1] = $phaset[$index2];
				$index1++;
				$totalCount++;
			}
			$index2++;
		}

		return $retVal;
	}

	private function dsin($x) {
		
		return sin(($x * pi()) / 180.0);
	}

	private function dcos($x) {
		
		return cos(($x * pi()) / 180.0);
	}

	private function jtog($td) {
		
		$td += 0.5;
		$z = floor($td);
		$f = $td - $z;
		if ($z < 2299161.0) {
			$a = $z;
		} else {
			$alpha = floor(($z - 1867216.25) / 36524.25);
			$a = $z + 1 + $alpha - floor($alpha / 4);
		}
		$b = $a + 1524;
		$c = floor(($b - 122.1) / 365.25);
		$d = floor(365.25 * $c);
		$e = floor(($b - $d) / 30.6001);
		$mm = floor(($e < 14) ? ($e - 1) : ($e - 13));
		$ij = ($td - floor($td)) * 86400.0;
		
		$nggd = new EnricoDate(floor($b - $d - floor(30.6001 * $e) + $f), $mm, floor(($mm > 2) ? ($c - 4716) : ($c - 4715)));
		return $nggd;
	}

	public function calculateWinterSolstice($year) {

		return $this->jtog(1721414.3920 + 365.2428898 * ($year - 1) - 0.0109650*(($year - 1) / 1000)*(($year - 1) / 1000) - 0.0084885*(($year - 1) / 1000)*(($year - 1) / 1000)*(($year - 1) / 1000) + (1/3));
	}
}

?>