<?php

namespace Application;

use Exception;
use Register;
use Label;

define("ASSET_SEPARATOR", ["=","<",">","!"]);
define("EXPONENT_SEPARATOR", ["^","**"]);
define("SIGNATURE_SEPARATOR", ["-","+"]);
define("PROPORTION_SEPARATOR", ":");
define("SUCH_THAT_SEPARATOR", "|");
define("EXTENSION_SEPARATOR", ".");
define("LIST_SEPARATOR", ",");

// Directory Path Helper
function A (?array $A = [__DIR__,__NAMESPACE__], ?string $B = DIRECTORY_SEPARATOR) : string
{
    $C = join ($B, $A);
    return $C;
}

// Resolves & Allocates Namespace Folders' Autoload Sequence; Initializing & Touching a Returned Register, or Exiting.
function B ()
{
    try
    {
        $A = A ();
        $B = is_dir ($A);
        if ($B)
        {
            $C = define("APPLICATION", $A);
            if ($C)
            {
                $D = A ([APPLICATION, A (["Autoload", "php"], EXTENSION_SEPARATOR)]);
                require_once $D;
                $E = new Register ();
                return $E;
            }
        }
    }
    catch (Exception $e)
    {

    }
    exit (1);
}

// Handles Autoload and Register Touch Sequence

$A = B ();

// Defines Constant A - The First Touched Register 

define("A", $A);


$B = [
    "Label",
    "Q|Quantity",
    "M|Mass,m",
    "kg|Kilogram",
    "L|Length,l,Distance,d",
    "m|Metre",
    "T|Time,t",
    "s|Second"
];

// Exhausts All Labels while Registering Descendant Registers for Labels@B, Quantities@D, Constraints@C, then Loads into Assignment Known Universal Constants as Registered Constraints

function C (array &$A)
{

    // Dynamically Names Labels with Labels, Constant B is the Register for Labels

    $B = array_shift ($A);
    $C = new Label ($B);
    $D = new Register ($C);
    $D->new ($C);
    A->attach ($C, $D);
    $E = A[$C];

    define ("B", $E);

    // Dynamically Names Quantities with Quantities, Constant D is the Register for Quantities

    $F = array_shift ($A);
    $G = Label::fromString ($F);
    $H = new Register ($G);
    B->new($G);
    A->attach($G, $H);
    $I = A[$G];

    define("D", $I);

    // Takes Pairs off the End of the Given Fundamental Base Types and their Respective Labels, then Registers them as Quantity@D

    while (count ($A) > 0)
    {
        $J = array_shift ($A);
        $K = Label::fromString ($J);
        $L = array_shift ($A);
        $M = Label::fromString ($L);
        B->new ($K);
        B->new ($M);
        D->attach ($K, $M);
    }

    // Universal Constants and Formatted into Constraints Register - and All Defined Quantities have their Related Constraints Mutated

    $N = new Label ("Constraint");
    $O = new Register ($N);
    B->new($N);
    A->attach($N, $O);
    $P = A[$N];

    define("C", $P);

    // Constraints in Nameable Constraint Notation

    $Q = [
        "ΔνCs	hyperfine transition frequency of 133Cs	9192631770 Hz",
        "c|Speed of Light=299792458 m/s",
        "h|Planck constant=6.62607015×10−34 J⋅s (J = kg⋅m2⋅s−2)",
        "e	elementary charge	1.602176634×10−19 C",
        "k	Boltzmann constant	1.380649×10−23 J/K",
        "NA	Avogadro constant	6.02214076×1023 mol−1",
        "Kcd	luminous efficacy of 540 THz radiation	683 lm/W",
        "Speed of Light|v<=299792458",
        "Planck Constant|E f^-1=6.62607015E-34",
        "Boltzmann Constant|E T^-1=1.380649E-23",
        "Gravitational Constant|F L^2 M^-2=6.67430E-11"
    ];

    foreach ($Q as $R)
    {
        $S = Constraint::fromString ($R);
        C->new($S);
    }
}

C ($B);

// QDA & U : B & C : L & R
// Quantity Dimensional Analysis & Universal : Base & Constant : Label & Register

/* Minimum Domain World Initializations Complete */


?>