<?php

namespace App\Controllers;

require 'vendor/autoload.php';
use App\Models\Course;
use App\Controllers\BaseController;
use Fpdf\Fpdf;

class CourseController extends BaseController
{
    public function list()
    {
        $obj = new Course();
        $courses = $obj->all();

        $template = 'courses';
        $data = [
            'items' => $courses
        ];

        $output = $this->render($template, $data);

        return $output;
    }

    public function viewCourse($course_code)
    {
        $courseObj = new Course();
        $course = $courseObj->find($course_code);
        $enrolees = $courseObj->getEnrolees($course_code);

        $template = 'single-course';
        $data = [
            'course' => $course,
            'enrolees' => $enrolees
        ];

        $output = $this->render($template, $data);

        return $output;
    }

    public function exportCourse($course_code) {
        $obj = new Course();
        
        // Get course details
        $course = $obj->find($course_code);  
        // Get enrollees for the course
        $enrollees = $obj->getEnrolees($course_code);
    
        // Create a new PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Set title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(33, 37, 41); 
        $pdf->Cell(0, 10, 'Course Information', 0, 1, 'C');
        
        // Add a line separator
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Line(10, 20, 200, 20); 
        
        // Line break
        $pdf->Ln(10);
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'Course Code:', 0, 0, 'L');
        $pdf->Cell(0, 10, $course->course_code, 0, 1, 'L');
        
        $pdf->Cell(50, 10, 'Course Name:', 0, 0, 'L');
        $pdf->Cell(0, 10, $course->course_name, 0, 1, 'L');
    
        $pdf->Cell(50, 10, 'Description:', 0, 0, 'L');
        $pdf->MultiCell(0, 10, $course->description, 0, 'L');  
        
        $pdf->Cell(50, 10, 'Credits:', 0, 0, 'L');
        $pdf->Cell(0, 10, $course->credits, 0, 1, 'L');
        
        // Line break
        $pdf->Ln(10);
        
        // List of enrollees title
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'List of Enrollees', 0, 1, 'C');  
    
        // Line break
        $pdf->Ln(5);
        
        // Define column widths
        $idWidth = 20;
        $firstNameWidth = 30;
        $lastNameWidth = 30;
        $emailWidth = 60;
        $dobWidth = 30;
        $sexWidth = 20;
    
        // Calculate total width of the table
        $totalWidth = $idWidth + $firstNameWidth + $lastNameWidth + $emailWidth + $dobWidth + $sexWidth;
    
        // Calculate starting X position for center alignment
        $startX = ($pdf->GetPageWidth() - $totalWidth) / 2;
    
        // Set header for enrollees table
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(100, 149, 237);  // Fill color for header
        $pdf->SetTextColor(255, 255, 255);  // Text color for header
        
        // Set X position for header
        $pdf->SetX($startX);
        $pdf->Cell($idWidth, 10, 'ID', 1, 0, 'C', true);
        $pdf->Cell($firstNameWidth, 10, 'First Name', 1, 0, 'C', true);
        $pdf->Cell($lastNameWidth, 10, 'Last Name', 1, 0, 'C', true);
        $pdf->Cell($emailWidth, 10, 'Email', 1, 0, 'C', true);
        $pdf->Cell($dobWidth, 10, 'Date of Birth', 1, 0, 'C', true);
        $pdf->Cell($sexWidth, 10, 'Sex', 1, 0, 'C', true);
        $pdf->Ln();
        
        // Reset font and colors for table content
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0); 
    
        if (!empty($enrollees)) {
            foreach ($enrollees as $index => $enrollee) {
                // Set X position for content cells
                $pdf->SetX($startX);  // Center the content as well
                $pdf->Cell($idWidth, 10, $enrollee["student_code"], 1, 0, 'C');
                $pdf->Cell($firstNameWidth, 10, $enrollee["first_name"], 1, 0, 'C');
                $pdf->Cell($lastNameWidth, 10, $enrollee["last_name"], 1, 0, 'C');
                $pdf->Cell($emailWidth, 10, $enrollee["email"], 1, 0, 'C');
                $pdf->Cell($dobWidth, 10, $enrollee["date_of_birth"], 1, 0, 'C');
                $pdf->Cell($sexWidth, 10, $enrollee["sex"], 1, 0, 'C');
                $pdf->Ln();
            }
        } else {
            // If no enrollees, display a message
            $pdf->SetX($startX); 
            $pdf->Cell(0, 10, 'No enrollees found for this course.', 1, 1, 'C');
        }
        
        // Output the PDF
        $pdf->Output('D', 'course_' . $course_code . '_enrollees.pdf');
    }
    
    
}
