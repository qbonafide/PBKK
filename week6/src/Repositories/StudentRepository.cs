using Microsoft.Data.SqlClient;
using StudentRegistration.Models;

namespace StudentRegistration.Repositories;

public class StudentRepository
{
    public List<Student> GetAll(string keyword = "")
    {
        var students = new List<Student>();

        const string sql = @"
            SELECT
                s.StudentId,
                s.NIM,
                s.Name,
                s.ProgramId,
                p.ProgramName,
                s.BirthDate,
                s.Address,
                s.PhoneNumber,
                s.CreatedAt,
                s.UpdatedAt
            FROM Students AS s
            INNER JOIN Programs AS p ON p.ProgramId = s.ProgramId
            WHERE @Keyword = ''
               OR s.NIM LIKE '%' + @Keyword + '%'
               OR s.Name LIKE '%' + @Keyword + '%'
               OR p.ProgramName LIKE '%' + @Keyword + '%'
            ORDER BY s.StudentId;";

        using var connection = new SqlConnection(DbConfig.ConnectionString);
        using var command = new SqlCommand(sql, connection);
        command.Parameters.Add("@Keyword", System.Data.SqlDbType.VarChar, 100).Value = keyword;
        connection.Open();

        using var reader = command.ExecuteReader();
        while (reader.Read())
        {
            students.Add(MapStudent(reader));
        }

        return students;
    }

    public int GetTotal()
    {
        const string sql = "SELECT COUNT(*) FROM Students;";

        using var connection = new SqlConnection(DbConfig.ConnectionString);
        using var command = new SqlCommand(sql, connection);
        connection.Open();

        return Convert.ToInt32(command.ExecuteScalar());
    }

    public bool NimExists(string nim, int excludeId = 0)
    {
        const string sql = @"
            SELECT COUNT(*)
            FROM Students
            WHERE NIM = @NIM
              AND (@ExcludeId = 0 OR StudentId <> @ExcludeId);";

        using var connection = new SqlConnection(DbConfig.ConnectionString);
        using var command = new SqlCommand(sql, connection);
        command.Parameters.Add("@NIM", System.Data.SqlDbType.VarChar, 20).Value = nim;
        command.Parameters.Add("@ExcludeId", System.Data.SqlDbType.Int).Value = excludeId;
        connection.Open();

        return Convert.ToInt32(command.ExecuteScalar()) > 0;
    }

    public void Insert(Student student)
    {
        const string sql = @"
            INSERT INTO Students
                (NIM, Name, ProgramId, BirthDate, Address, PhoneNumber)
            VALUES
                (@NIM, @Name, @ProgramId, @BirthDate, @Address, @PhoneNumber);";

        ExecuteStudentCommand(sql, student);
    }

    public void Update(Student student)
    {
        const string sql = @"
            UPDATE Students
            SET NIM = @NIM,
                Name = @Name,
                ProgramId = @ProgramId,
                BirthDate = @BirthDate,
                Address = @Address,
                PhoneNumber = @PhoneNumber,
                UpdatedAt = SYSDATETIME()
            WHERE StudentId = @StudentId;";

        using var connection = new SqlConnection(DbConfig.ConnectionString);
        using var command = CreateStudentCommand(sql, connection, student);
        command.Parameters.Add("@StudentId", System.Data.SqlDbType.Int).Value = student.StudentId;
        connection.Open();

        if (command.ExecuteNonQuery() == 0)
        {
            throw new InvalidOperationException("Data mahasiswa tidak ditemukan.");
        }
    }

    public void Delete(int studentId)
    {
        const string sql = "DELETE FROM Students WHERE StudentId = @StudentId;";

        using var connection = new SqlConnection(DbConfig.ConnectionString);
        using var command = new SqlCommand(sql, connection);
        command.Parameters.Add("@StudentId", System.Data.SqlDbType.Int).Value = studentId;
        connection.Open();

        if (command.ExecuteNonQuery() == 0)
        {
            throw new InvalidOperationException("Data mahasiswa tidak ditemukan.");
        }
    }

    private static void ExecuteStudentCommand(string sql, Student student)
    {
        using var connection = new SqlConnection(DbConfig.ConnectionString);
        using var command = CreateStudentCommand(sql, connection, student);
        connection.Open();
        command.ExecuteNonQuery();
    }

    private static SqlCommand CreateStudentCommand(
        string sql,
        SqlConnection connection,
        Student student)
    {
        var command = new SqlCommand(sql, connection);
        command.Parameters.Add("@NIM", System.Data.SqlDbType.VarChar, 20).Value = student.NIM;
        command.Parameters.Add("@Name", System.Data.SqlDbType.VarChar, 100).Value = student.Name;
        command.Parameters.Add("@ProgramId", System.Data.SqlDbType.Int).Value = student.ProgramId;
        command.Parameters.Add("@BirthDate", System.Data.SqlDbType.Date).Value =
            student.BirthDate.HasValue ? student.BirthDate.Value : DBNull.Value;
        command.Parameters.Add("@Address", System.Data.SqlDbType.VarChar, 250).Value =
            string.IsNullOrWhiteSpace(student.Address) ? DBNull.Value : student.Address;
        command.Parameters.Add("@PhoneNumber", System.Data.SqlDbType.VarChar, 20).Value =
            string.IsNullOrWhiteSpace(student.PhoneNumber) ? DBNull.Value : student.PhoneNumber;
        return command;
    }

    private static Student MapStudent(SqlDataReader reader)
    {
        return new Student
        {
            StudentId = reader.GetInt32(reader.GetOrdinal("StudentId")),
            NIM = reader.GetString(reader.GetOrdinal("NIM")),
            Name = reader.GetString(reader.GetOrdinal("Name")),
            ProgramId = reader.GetInt32(reader.GetOrdinal("ProgramId")),
            ProgramName = reader.GetString(reader.GetOrdinal("ProgramName")),
            BirthDate = reader.IsDBNull(reader.GetOrdinal("BirthDate"))
                ? null
                : reader.GetDateTime(reader.GetOrdinal("BirthDate")),
            Address = reader.IsDBNull(reader.GetOrdinal("Address"))
                ? string.Empty
                : reader.GetString(reader.GetOrdinal("Address")),
            PhoneNumber = reader.IsDBNull(reader.GetOrdinal("PhoneNumber"))
                ? string.Empty
                : reader.GetString(reader.GetOrdinal("PhoneNumber")),
            CreatedAt = reader.GetDateTime(reader.GetOrdinal("CreatedAt")),
            UpdatedAt = reader.IsDBNull(reader.GetOrdinal("UpdatedAt"))
                ? null
                : reader.GetDateTime(reader.GetOrdinal("UpdatedAt"))
        };
    }
}
